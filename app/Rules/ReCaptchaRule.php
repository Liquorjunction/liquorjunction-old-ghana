<?php

namespace App\Rules;

use App\Services\ReCaptchaService;
use Illuminate\Contracts\Validation\Rule;

class ReCaptchaRule implements Rule
{
    /**
     * @var string|null
     */
    protected $action;

    /**
     * @var float|null
     */
    protected $minScore;

    /**
     * @var string
     */
    protected $errorMessage = 'Security verification failed. Please try again.';

    /**
     * Create a new rule instance.
     *
     * @param string|null $action Expected reCAPTCHA v3 action (e.g. 'register', 'login')
     * @param float|null $minScore Minimum acceptable bot score (default 0.5)
     */
    public function __construct(?string $action = null, ?float $minScore = null)
    {
        $this->action = $action;
        $this->minScore = $minScore;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var ReCaptchaService $service */
        $service = app(ReCaptchaService::class);

        // If captcha is not enabled in admin or .env, always pass smoothly
        if (!$service->isEnabled()) {
            return true;
        }

        $result = $service->verify($value, $this->action, request()->ip(), $this->minScore);

        if (!$result['success']) {
            $this->errorMessage = $result['message'] ?? 'Security verification failed. Please try again.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage;
    }
}
