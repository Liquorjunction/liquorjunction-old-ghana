 <?php 
$category_id = Session::get('category_id');
$most_view = Session::get('most_view');
// echo "<pre>";print_r($most_view);exit();
?>
 <div class="filter-content">
                                <div class="check-group">
                                  @if($most_view == 0)
                                    <input class="form-check-input" type="checkbox" value="0" onclick="return mostViewed(1);" id="flexCheckDefault26">
                                    <label class="form-check-label" for="flexCheckDefault26">Most viewed</label>
                                    @else
                                    <input class="form-check-input" type="checkbox" value="1" onclick="return mostViewedRemove(0);" id="flexCheckDefault26" checked>
                                    <label class="form-check-label" for="flexCheckDefault26">Most viewed</label>
                                    @endif
                                </div>
                            </div>
                            <div class="filter-content">
                                <h6>Categories</h6>
                                <ul class="mb-0">
                                    <li>
                                        <div class="radio-group">
                                            <input id="radioDefault" value="all" class="test" {{ ($category_id == "all") ? 'checked' : "" }}  type="radio" name="common-radio">
                                            <label for="radioDefault">All</label>
                                        </div>
                                    </li>
                                    @foreach($categoryData as $key=>$category)
                                        <li>
                                        <div class="radio-group">
                                            <input id="radioDefault{{$key}}" {{ ($category_id == $category->id) ? 'checked' : "" }} class="test" name="category_id" value="{{@$category->id}}" type="radio">
                                            <label for="radioDefault{{$key}}">{{@$category->title}}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                    
                                </ul>                                
                            </div>