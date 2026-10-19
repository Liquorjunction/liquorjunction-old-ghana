<div class="store-location-block">
                                <div class="store-info-block">
                                    <span class="d-block store-image">
                                         @php($storeImage = ($storeData->profile)?'uploads/customer/'.$storeData->profile:"assets/frontend/images/store_image.png")
                                        <img src="{{@$storeImage}}" alt="store-logo" title="Store Logo" />
                                    </span>
                                    <h4 class="mb-0">{{@$storeData->store_name}}</h4>
                                </div>
                                <div class="store-timmings">
                                    <h6>Opening Timings</h6>
                                    <ul>
                                       @foreach($storeTiming as $timing)

                                        <li class="timmings">
                                            <p class="body-normal">{{@$timing->week_name}}</p>
                                            @if($timing->start_time == "00:00:00")
                                            <span class="body-normal red-text">Close</span>
                                            @else
                                            <span class="body-normal">{{date('h:i A', strtotime($timing->start_time));}} -  {{date('h:i A', strtotime($timing->end_time));}}</span>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <a href="javascript:void(0)" class="store-location-block-cross"><i class="fa-solid fa-xmark"></i></a>
                            </div>