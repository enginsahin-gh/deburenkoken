@extends('layout.main')
@section('content')
    <div class="page-header">
        <div class="container"><h1>Beoordeling</h1></div>
    </div>

    <div class="container single-cook">
        <div class="row">
            <div class="col-1">
                <a href="{{url()->previous()}}" class="tarug">< Terug</a>
                <!-- <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="float-right">X</a> -->
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <div class="product-showcase">
                    <img src="{{$cook->user->image?->getCompletePath()}}" class="dish-img mb-20"/>
                    <!-- <img src="{{asset('img/eleven.jpg')}}" class="dish-img mb-20" /> -->
                </div>
            </div>


            <div class="col-8">   
                <div class="row mt-n3">
                    <div class="col-12">
                        <small class="blue-clr">Actief sinds {{$cook->user->getCreatedAt()->translatedFormat('d F Y')}}</small>
                    </div>
                </div>             
                <div class="row">
                    <div class="col-12 cook-title">
                        <h2>{{$cook->user->getUsername()}}</h2>
                        <a href="{{route('search.cooks.detail.review', $cook->getUuid())}}">
                            @php $rating = $cook->user->reviews->avg('rating') ?? 0; @endphp
                            @foreach(range(1,5) as $i)
                                <span class="fa-stack" style="width:1em">
                                    @if($rating <= 0)
                                        <i class="far fa-star fa-stack-1x"></i>
                                    @endif
                                    
                                    @if($rating > 0)
                                        @if($rating >0.5)
                                            <i class="fas fa-star fa-stack-1x"></i>
                                        @else
                                            <i class="fas fa-star-half fa-stack-1x"></i>
                                        @endif
                                    @endif
                                    @php $rating--; @endphp
                                </span>
                            @endforeach
                            <span class="review-count">({{$cook->user->reviews->count()}})</span>
                        </a>
                    </div>                    
                </div>
                
                <div class="details">
                    <h3>Gemiddelde beoordeling</h3>
                    <p>Op basis van {{$cook->user->reviews->count()}} beoordelingen</p>
                </div>

                <div class="row mt-20 mb-30">
                    <div class="col-12">
                        @php
                            $totalReviews = $cook->user->reviews->count();
                            $orderedReviews = $cook->user->reviews->groupBy('rating');
                            $one = [
                                'count' => 0,
                                'percentage' => 0
                            ];
                            $two = [
                                'count' => 0,
                                'percentage' => 0
                            ];
                            $three = [
                                'count' => 0,
                                'percentage' => 0
                            ];
                            $four = [
                                'count' => 0,
                                'percentage' => 0
                            ];
                            $five = [
                                'count' => 0,
                                'percentage' => 0
                            ];

                            foreach ($orderedReviews as $key => $item) {
                                if ($key === 1) {
                                    $one['count'] = $item->count();
                                    $one['percentage'] = ($totalReviews / $totalReviews) * 100;
                                }
                                if ($key === 2) {
                                    $two['count'] = $item->count();
                                    $two['percentage'] = ($totalReviews / $totalReviews) * 100;
                                }
                                if ($key === 3) {
                                    $three['count'] = $item->count();
                                    $three['percentage'] = ($totalReviews / $totalReviews) * 100;
                                }
                                if ($key === 4) {
                                    $four['count'] = $item->count();
                                    $four['percentage'] = ($totalReviews / $totalReviews) * 100;
                                }
                                if ($key === 5) {
                                    $five['count'] = $item->count();
                                    $five['percentage'] = ($totalReviews / $totalReviews) * 100;
                                }
                            }
                        @endphp

                        <div class="row">
                            <div class="col-2 rtl">
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </div>
                            <div class="col-10 d-inline-flex">
                                <div class="progress w-80">
                                    <div class="progress-bar progress-bar-five"></div>
                                </div>
                                <div class="col">
                                    {{$five['count']}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2 rtl">
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </div>
                            <div class="col-10 d-inline-flex">
                                <div class="progress w-80">
                                    <div class="progress-bar progress-bar-four"></div>
                                </div>
                                <div class="col">
                                    {{$four['count']}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2 rtl">
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </div>
                            <div class="col-10 d-inline-flex">
                                <div class="progress w-80">
                                    <div class="progress-bar progress-bar-three"></div>
                                </div>
                                <div class="col">
                                    {{$three['count']}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2 rtl">
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </div>
                            <div class="col-10 d-inline-flex">
                                <div class="progress w-80">
                                    <div class="progress-bar progress-bar-two"></div>
                                </div>
                                <div class="col">
                                    {{$two['count']}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2 rtl">
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                </span>
                                <span class="fa-stack" style="width:1em">
                                    <i class="far fa-star fa-stack-1x"></i>
                                    <i class="fas fa-star fa-stack-1x"></i>
                                </span>
                            </div>
                            <div class="col-10 d-inline-flex">
                                <div class="progress w-80">
                                    <div class="progress-bar progress-bar-one"></div>
                                </div>
                                <div class="col">
                                    {{$one['count']}}
                                </div>
                            </div>
                        </div>


                        <div class="row mt-10">
                            @foreach($cook->user->reviews as $review)
                                <div class="col-12 box-review">
                                    <div class="row">
                                        <div class="col">
                                            @if(!$review->isAnonymous())
                                                <h4>{{$review->order?->client?->getName()}}</h4>
                                            @else
                                                <h4>Anoniem</h4>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">
                                            {{$review->getCreatedAt()->translatedFormat('d-m-Y')}}
                                        </div>
                                        <div class="col-3">
                                            <b>{{$review->order?->dish->getTitle()}}</b>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-10">
                                            {{$review->getReview()}}
                                        </div>
                                        <div class="col-2">
                                            @php $rating = $review->getRating(); @endphp
                                            @foreach(range(1,5) as $i)
                                                <span class="fa-stack" style="width:1em">
                                                    @if($rating <= 0)
                                                        <i class="far fa-star fa-stack-1x"></i>
                                                    @endif
                                                    @if($rating > 0)
                                                        @if($rating > 0.5)
                                                            <i class="fas fa-star fa-stack-1x"></i>
                                                        @else
                                                            <i class="fas fa-star-half fa-stack-1x"></i>
                                                        @endif
                                                    @endif
                                                    @php $rating--; @endphp
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('page.style')
    <style>
        .progress{
            position: relative;
            top: 8px;
        }
        .progress-bar {
            height: 16px;
            border-radius: 4px;
            background-image: -webkit-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: -moz-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: -o-linear-gradient(top, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.05));
            -webkit-transition: 0.4s linear;
            -moz-transition: 0.4s linear;
            -o-transition: 0.4s linear;
            transition: 0.4s linear;
            -webkit-transition-property: width, background-color;
            -moz-transition-property: width, background-color;
            -o-transition-property: width, background-color;
            transition-property: width, background-color;
            -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 1px 1px rgba(0, 0, 0, 0.25), inset 0 1px rgba(255, 255, 255, 0.1);
        }

        .progress-bar-one {
            width: {{$one['percentage']}}%;
            background-color: #f1713b;
        }

        .progress-bar-two {
            width:  {{$two['percentage']}}%;
            background-color: #f1713b;
        }

        .progress-bar-three {
            width:  {{$three['percentage']}}%;
            background-color: #f1713b;
        }

        .progress-bar-four {
            width:  {{$four['percentage']}}%;
            background-color: #f1713b;
        }

        .progress-bar-five {
            width:  {{$five['percentage']}}%;
            background-color: #f1713b;
        }
    </style>
@endsection
