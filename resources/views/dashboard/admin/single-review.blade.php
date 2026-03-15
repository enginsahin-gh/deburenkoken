@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Reviews</h1></div>
    </div>
    <div class="dashboard container">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="50%">Review gegeven door:</td>
                    <td width="50%">{{$review->client->getName()}} - {{$review->client->getEmail()}} - {{$review->client->getPhoneNumber()}}</td>
                </tr>
                <tr>
                    <td>Review gegeven op:</td>
                    <td>{{$review->getCreatedAt()->translatedFormat('d-m-Y')}}</td>
                </tr>
                <tr>
                    <td>Review voor gerecht:</td>
                    <td>{{$review->order->dish->getTitle()}}</td>
                </tr>
                <tr>
                    <td>Gerecht gemaakt door:</td>
                    <td>{{$review->order->user->getUsername()}}</td>
                </tr>
                <tr>
                    <td>Reviewscore:</td>
                    <td>{{$review->getRating()}}</td>
                </tr>
                <tr>
                    <td colspan="2">Reviewtekst:</td>
                </tr>
                <tr>
                    <td colspan="2">{{$review->getReview()}}</td>
                </tr>
            </tbody>
        </table>


      
        <?php $rating = $review->getRating() ?>
        <div class="row mt-10">
            <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin-top: 10px;">
                <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                    <tbody>
                    <tr>
                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                <div class="star" lang="x-star-divbox" style="color: #f16e3c; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="2">
                                    @if($rating < 5)
                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                    @else
                                        <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                <div class="star" lang="x-star-divbox" style="color: #f16e3c; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="3">
                                    @if($rating < 4)
                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                    @else
                                        <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                <div class="star" lang="x-star-divbox" style="color: #f16e3c; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="4">
                                    @if($rating < 3)
                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                    @else
                                        <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                <div class="star" lang="x-star-divbox" style="color: #f16e3c; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="5">
                                    @if($rating < 2)
                                        <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                    @else
                                        <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 0;vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                <div class="star" lang="x-star-divbox" style="color: #f16e3c; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                    <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <a href="{{route('dashboard.admin.reviews.delete', ['uuid' => $review->getUuid()])}}" class="btn btn-light">Review verwijderen</a>
            </div>
        </div>
    </div>
@endsection


