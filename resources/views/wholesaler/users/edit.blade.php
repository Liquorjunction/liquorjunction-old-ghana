@extends('wholesaler.layouts.master')
@section('title','Edit Profile')
@section('content')
    <div class="padding edit-package edit-user">
        <div class="box">
            <div class="box-header dker">
                <h3><i class="material-icons">&#xe3c9;</i> Edit Profile </h3>
                <small>
                    <a href="{{ route('adminHome') }}">{{ __('backend.home') }}</a> / Edit Profile
                    <!-- <a href="javascript:void(0)">Edit Profile</a> -->
                </small>
            </div>
            <div class="box-tool">
                <ul class="nav">
                    <li class="nav-item inline">
                        <a class="nav-link" href="{{route("users")}}">
                            <i class="material-icons md-18">×</i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body">
                {{Form::open(['route'=>['userswholesalerUpdate',$Users->id],'method'=>'POST', 'files' => true])}}

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">Store Name<span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('store_name',$Users->store_name, array('placeholder' => '','class' => 'form-control','id'=>'store_name')) !!}
                        @if ($errors->has('store_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('store_name') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">{!!  __('backend.firstname') !!} <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('first_name',$Users->first_name, array('placeholder' => '','class' => 'form-control','id'=>'first_name')) !!}
                        @if ($errors->has('first_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('first_name') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">{!!  __('backend.lastname') !!} <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('last_name',$Users->last_name, array('placeholder' => '','class' => 'form-control','id'=>'last_name')) !!}
                        @if ($errors->has('last_name'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('last_name') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email"
                           class="col-sm-2 form-control-label">{!!  __('backend.loginEmail') !!} <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::email('email',$Users->email, array('placeholder' => '','class' => 'form-control','id'=>'email')) !!}
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('email') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">{!!  __('backend.phone') !!} <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('phone',$Users->phone, array('placeholder' => '','class' => 'form-control','id'=>'phone')) !!}
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('phone') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">ABN Number <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('abn_number',$Users->abn_number, array('placeholder' => '','class' => 'form-control','id'=>'abn_number')) !!}
                        @if ($errors->has('abn_number'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('abn_number') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                 <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">Description <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                         <textarea class="form-control  h-auto py-7 px-6 rounded-lg" type="text" name="store_description" id="store_description" placeholder="Store Description" value="{{@$Users->store_description}}" autocomplete="off">{{@$Users->store_description}}</textarea>
                        @if ($errors->has('store_description'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('store_description') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">Country <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('country',$Users->country, array('placeholder' => '','class' => 'form-control','id'=>'country')) !!}
                        @if ($errors->has('country'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('country') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">State <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('state',$Users->states, array('placeholder' => '','class' => 'form-control','id'=>'state')) !!}
                        @if ($errors->has('state'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('state') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">City <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('city',$Users->city, array('placeholder' => '','class' => 'form-control','id'=>'city')) !!}
                        @if ($errors->has('city'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('city') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">Street Address <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('street_address',$Users->street_address, array('placeholder' => '','class' => 'form-control','id'=>'street_address')) !!}
                        @if ($errors->has('street_address'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('street_address') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name"
                           class="col-sm-2 form-control-label">Zip Code <span class="valid_field">*</span>
                    </label>
                    <div class="col-sm-10">
                        {!! Form::text('post_code',$Users->post_code, array('placeholder' => '','class' => 'form-control','id'=>'post_code')) !!}
                        @if ($errors->has('post_code'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('post_code') }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{--<div class="form-group row">
                    <label for="password"
                           class="col-sm-2 form-control-label">{!!  __('backend.loginPassword') !!}
                    </label>
                    <div class="col-sm-10">
                    <input type="password" name="password" class="form-control" autocomplete="new-password">

                    </div>
                </div>--}}

                <div class="form-group row">
                    <label for="photo_file"
                           class="col-sm-2 form-control-label">{!!  __('backend.topicPhoto') !!} </label>
                    <div class="col-sm-10">
                        @if($Users->profile!="")
                            <div class="row">
                                <div class="col-sm-12 images">
                                    <div id="user_photo" class="col-sm-4 box p-a-xs">
                                        <?php 
                                        if ($Users->user_type==1) { ?>
                                            <a target="_blank"
                                           href="{{ asset('uploads/users/'.$Users->profile) }}"><img
                                                src="{{ asset('uploads/users/'.$Users->profile) }}"
                                                class="img-responsive">
                                        </a>
                                        <?php }else{ ?>
                                            <a target="_blank"
                                           href="{{ asset('uploads/customer/'.$Users->profile) }}"><img
                                                src="{{ asset('uploads/customer/'.$Users->profile) }}"
                                                class="img-responsive">
                                        </a>
                                       <?php }
                                        ?>
                                        <br>
                                        <div class="delete">
                                            <a onclick="document.getElementById('user_photo').style.display='none';document.getElementById('photo_delete').value='1';document.getElementById('undo').style.display='block';"
                                            class="btn btn-sm btn-default">{!!  __('backend.delete') !!}</a>
                                            {{ $Users->photo }}
                                        </div>
                                    </div>
                                    <div id="undo" class="col-sm-4 p-a-xs" style="display: none">
                                        <a onclick="document.getElementById('user_photo').style.display='block';document.getElementById('photo_delete').value='0';document.getElementById('undo').style.display='none';">
                                            <i class="material-icons">&#xe166;</i> {!!  __('backend.undoDelete') !!}
                                        </a>
                                    </div>

                                    {!! Form::hidden('photo_delete','0', array('id'=>'photo_delete')) !!}
                                </div>
                            </div>
                        @endif

                        {!! Form::file('profile_picture', array('class' => 'form-control','id'=>'profile_picture','accept'=>'image/*','style' => 'margin-left: -12px;
')) !!}
                        @if ($errors->has('profile_picture'))
                            <span class="help-block">
                                <span  style="color: red;" class='validate'>{{ $errors->first('profile_picture') }}</span>
                            </span>
                        @endif
                        <small>
                            <i class="material-icons">&#xe8fd;</i>
                            {!!  __('backend.imagesTypes') !!}
                        </small>
                    </div>
                </div>

                


                <div class="form-group row m-t-md">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary m-t"><i class="material-icons">
                                &#xe31b;</i> {!! __('backend.update') !!}</button>
                        <a href="{{route('adminwholesalerHome')}}"
                           class="btn btn-default m-t"><i class="material-icons">
                                &#xe5cd;</i> {!! __('backend.cancel') !!}</a>
                    </div>
                </div>

                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
