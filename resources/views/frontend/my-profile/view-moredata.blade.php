<div class="modal-dialog">
<div class="modal-content">
            <div class="modal-header">
                <h4>{{@$viewMoreData->title}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="quote-item-content">                    
                    <div class="quote-item-img d-block">
                        <img src="{{ asset('uploads/quote/').'/'.$viewMoreData->quote_image }}" alt="color-service">
                    </div>
                    <ul class="quote-item-info">
                        <li class="body-normal text-black d-inline-block">
                            Material Category : 
                            <span class="body-normal text-bold text-black">{{@$viewMoreData->material_category_name}}</span>
                        </li>
                        <li class="body-normal text-black d-inline-block">
                            Zip Code : 
                            <span class="body-normal text-bold text-black">{{@$viewMoreData->post_code}}</span>
                        </li>
                        <li class="body-normal text-black d-inline-block mb-0">
                            Time Frame : 
                            <span class="body-normal text-bold text-black">{{@$viewMoreData->time_frame_name}}</span>
                        </li>
                    </ul>
                    <p class="body-normal text-grey mb-0">{{@$viewMoreData->description}} </p>
                </div>
            </div>
        </div>
    </div>