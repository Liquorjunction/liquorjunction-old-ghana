<?php

namespace App\Services;

use App\Models\GuestUser;
use App\Models\MainUser;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GuestUserService
{
    /**
     * Create or update guest user in `guest_users` table.
     */
    public function createOrUpdateGuest(array $data): GuestUser
    {
        $normalizedEmail = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
        $name = trim($data['name'] ?? 'Guest User');
        $phone = isset($data['phone']) ? trim($data['phone']) : null;

        $guest = null;
        if (!empty($normalizedEmail)) {
            $guest = GuestUser::where('email', $normalizedEmail)->first();
        } elseif (!empty($phone)) {
            $guest = GuestUser::where('phone', $phone)->first();
        }

        // Generate synthetic placeholder email if user checked out using phone only
        if (!$normalizedEmail) {
            $normalizedEmail = $phone ? ($phone . '@guest.liquorjunction.com') : ('guest_' . uniqid() . '@guest.liquorjunction.com');
        }

        if ($guest) {
            if (!$guest->isLinked()) {
                $guest->name = $name ?: 'Guest User';
            }
            if ($phone) {
                $guest->phone = $phone;
            }
            if (empty($guest->token)) {
                $guest->token = Str::random(40);
            }
            $guest->increment('orders_count');
            $guest->update(['last_order_at' => now()]);
        } else {
            $guest = GuestUser::create([
                'email'         => $normalizedEmail,
                'name'          => $name ?: 'Guest User',
                'phone'         => $phone,
                'token'         => Str::random(40),
                'orders_count'  => 1,
                'last_order_at' => now(),
            ]);
        }

        return $guest->fresh();
    }

    /**
     * Link previous guest orders to a registered user upon account verification/login.
     */
    public function linkGuestOrdersToUser(MainUser $user): int
    {
        if (empty($user->email) && empty($user->phone)) {
            return 0;
        }

        $normalizedEmail = !empty($user->email) ? strtolower(trim($user->email)) : null;
        $userPhone = !empty($user->phone) ? trim($user->phone) : null;

        return DB::transaction(function () use ($user, $normalizedEmail, $userPhone) {
            $guest = null;
            if ($normalizedEmail) {
                $guest = GuestUser::where('email', $normalizedEmail)
                    ->orWhere('email', $userPhone ? $userPhone . '@guest.liquorjunction.com' : null)
                    ->first();
            }
            if (!$guest && $userPhone) {
                $guest = GuestUser::where('phone', $userPhone)->first();
            }

            if ($guest) {
                $guest->update([
                    'registered_user_id' => $user->id,
                    'name'               => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                ]);
            }

            // Find all unlinked guest orders matching this guest user or matching customer_email/phone in order_info
            $ordersToLink = Order::where(function ($q) use ($guest, $normalizedEmail, $userPhone) {
                if ($guest) {
                    $q->where('guest_user_id', $guest->id);
                }
                if ($normalizedEmail) {
                    $q->orWhere(function ($sub) use ($normalizedEmail) {
                        $sub->where('client_type', 'guest')
                            ->whereHas('orderInfo', function ($infoQuery) use ($normalizedEmail) {
                                $infoQuery->whereRaw('LOWER(customer_email) = ?', [$normalizedEmail]);
                            });
                    });
                }
                if ($userPhone) {
                    $q->orWhere(function ($sub) use ($userPhone) {
                        $sub->where('client_type', 'guest')
                            ->whereHas('orderInfo', function ($infoQuery) use ($userPhone) {
                                $infoQuery->where('customer_mobile', 'like', '%' . $userPhone . '%');
                            });
                    });
                }
            })
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', '!=', $user->id)
                  ->orWhere('client_type', 'guest');
            })
            ->get();

            if ($ordersToLink->isEmpty()) {
                return 0;
            }

            $orderIds = $ordersToLink->pluck('id');

            // Update orders to point to registered user
            Order::whereIn('id', $orderIds)->update([
                'user_id'     => $user->id,
                'client_type' => 'registered',
            ]);

            // Update customer_id in order_detail table
            DB::table('order_detail')
                ->whereIn('order_id', $orderIds)
                ->update(['customer_id' => $user->id]);

            Log::info('Successfully linked guest orders to registered user.', [
                'user_id'      => $user->id,
                'email'        => $normalizedEmail,
                'phone'        => $userPhone,
                'orders_count' => $orderIds->count(),
                'order_ids'    => $orderIds->toArray(),
            ]);

            return $orderIds->count();
        });
    }

    /**
     * Check whether an email or phone number belongs to an existing registered account.
     */
    public function checkExistingUser(?string $email, ?string $phone): array
    {
        $normalizedEmail = !empty($email) ? strtolower(trim($email)) : null;
        $normalizedPhone = !empty($phone) ? trim($phone) : null;

        $emailExists = false;
        $phoneExists = false;

        if ($normalizedEmail) {
            $emailExists = MainUser::whereRaw('LOWER(email) = ?', [$normalizedEmail])
                ->where('is_guest_user', 0)
                ->where('user_type', 1)
                ->exists();
        }

        if ($normalizedPhone) {
            $digitsOnly = preg_replace('/[^\d]/', '', $normalizedPhone);
            if (strlen($digitsOnly) >= 7) {
                $phoneExists = MainUser::where(function ($q) use ($normalizedPhone, $digitsOnly) {
                    $q->where('phone', $normalizedPhone)
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?", ['%' . substr($digitsOnly, -8)]);
                })
                ->where('is_guest_user', 0)
                ->where('user_type', 1)
                ->exists();
            }
        }

        $exists = $emailExists || $phoneExists;

        $matchedBy = null;
        if ($emailExists && $phoneExists) {
            $matchedBy = 'both';
        } elseif ($emailExists) {
            $matchedBy = 'email';
        } elseif ($phoneExists) {
            $matchedBy = 'phone';
        }

        $message = null;
        if ($matchedBy === 'both') {
            $message = 'This email address or mobile number is already registered. Please log in using your existing account to continue.';
        } elseif ($matchedBy === 'email') {
            $message = 'You are already registered. Please log in to your account.';
        } elseif ($matchedBy === 'phone') {
            $message = 'This phone number is already registered. Please log in or use a different phone number.';
        }

        return [
            'exists'        => $exists,
            'is_registered' => $exists,
            'email_exists'  => $emailExists,
            'phone_exists'  => $phoneExists,
            'matched_by'    => $matchedBy,
            'email'         => $emailExists ? $normalizedEmail : null,
            'phone'         => $phoneExists ? $normalizedPhone : null,
            'message'       => $message,
        ];
    }

    /**
     * Check whether a guest user has reached the daily order limit for the current date.
     *
     * @param int|null $guestId
     * @param string|null $email
     * @param string|null $phone
     * @return array ['allowed' => bool, 'limit' => int, 'count' => int, 'message' => string|null]
     */
    public function checkDailyOrderLimit(?int $guestId = null, ?string $email = null, ?string $phone = null): array
    {
        $setting = \App\Models\Setting::find(1);
        $dailyLimit = $setting ? (int) ($setting->guest_daily_order_limit ?? 0) : 0;

        // 0 means unlimited orders allowed
        if ($dailyLimit <= 0) {
            return [
                'allowed' => true,
                'limit'   => 0,
                'count'   => 0,
                'message' => null,
            ];
        }

        $guest = null;
        if (!empty($guestId)) {
            $guest = GuestUser::find($guestId);
        }

        $normalizedEmail = !empty($email) ? strtolower(trim($email)) : ($guest ? strtolower(trim($guest->email)) : null);
        $phoneInput = !empty($phone) ? trim($phone) : ($guest ? trim($guest->phone) : null);
        $digitsOnly = $phoneInput ? preg_replace('/[^\d]/', '', $phoneInput) : null;
        $phoneSuffix = ($digitsOnly && strlen($digitsOnly) >= 7) ? substr($digitsOnly, -8) : null;

        // Count all orders placed on current date (regardless of order status)
        $todayQuery = Order::query()->whereDate('created_at', now()->toDateString());

        $todayQuery->where(function ($q) use ($guestId, $normalizedEmail, $phoneSuffix, $phoneInput) {
            $hasCondition = false;

            if (!empty($guestId)) {
                $q->where('guest_user_id', $guestId);
                $hasCondition = true;
            }

            if (!empty($normalizedEmail) || !empty($phoneSuffix) || !empty($phoneInput)) {
                $subCallback = function ($sub) use ($normalizedEmail, $phoneSuffix, $phoneInput) {
                    $sub->where('client_type', 'guest');
                    $sub->where(function ($infoQuery) use ($normalizedEmail, $phoneSuffix, $phoneInput) {
                        $hasInfo = false;
                        if (!empty($normalizedEmail)) {
                            $infoQuery->whereHas('orderInfo', function ($iq) use ($normalizedEmail) {
                                $iq->whereRaw('LOWER(TRIM(customer_email)) = ?', [$normalizedEmail]);
                            });
                            $hasInfo = true;
                        }
                        if (!empty($phoneSuffix)) {
                            $phoneClause = function ($iq) use ($phoneSuffix) {
                                $iq->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(customer_mobile, ' ', ''), '-', ''), '(', ''), ')', '') LIKE ?", ['%' . $phoneSuffix]);
                            };
                            if ($hasInfo) {
                                $infoQuery->orWhereHas('orderInfo', $phoneClause);
                            } else {
                                $infoQuery->whereHas('orderInfo', $phoneClause);
                                $hasInfo = true;
                            }
                        } elseif (!empty($phoneInput)) {
                            $phoneClause = function ($iq) use ($phoneInput) {
                                $iq->where('customer_mobile', 'LIKE', '%' . $phoneInput . '%');
                            };
                            if ($hasInfo) {
                                $infoQuery->orWhereHas('orderInfo', $phoneClause);
                            } else {
                                $infoQuery->whereHas('orderInfo', $phoneClause);
                            }
                        }
                    });
                };

                if ($hasCondition) {
                    $q->orWhere($subCallback);
                } else {
                    $q->where($subCallback);
                }
            }
        });

        $todayOrdersCount = $todayQuery->count();

        if ($todayOrdersCount >= $dailyLimit) {
            return [
                'allowed' => false,
                'limit'   => $dailyLimit,
                'count'   => $todayOrdersCount,
                'message' => "Daily guest limit ({$dailyLimit} order) reached. Log in or register to place more orders.",
            ];
        }

        return [
            'allowed' => true,
            'limit'   => $dailyLimit,
            'count'   => $todayOrdersCount,
            'message' => null,
        ];
    }
}
