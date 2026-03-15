@extends('layout.main')

@section('content')
<style>
    .btn-review {
        background: linear-gradient(to right, #f3723b 0%, #e54750 100%);
        color: #fff;
        border: 2px solid #f3723b;
        padding: 8px 15px;
        width: 200px;
        margin: 10px auto 30px;
        border-radius: 6px;
    }
    .btn-wrapper {
        display: flex;
        justify-content: center;
    }
    .char-counter {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
        text-align: right;
    }
</style>
    <section class="clearfix pt-100">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1>Beoordeling succesvol ingediend</h1>
                </div>
            </div>
            <div class="row mt-70">
                <div class="col-7 mx-auto text-center">
                    <div style="width: 100%; text-align: center; float: left;">
                        <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin: 0 auto; margin-top: 10px;">
                            <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                                <tbody>
                                    <tr>
                                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                <div class="star" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="2">
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
                                                <div class="star" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="3">
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
                                                <div class="star" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="4">
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
                                                <div class="star" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="5">
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
                                                <div class="star" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                                    <div lang="x-full-star" style="width:auto; overflow:visible;float:none; display:block;">★</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-70">
                <div class="col-7 mx-auto text-center">
                    <p>Bedankt voor het achterlaten van je beoordeling, we stellen het zeer op prijs. Als je een review wilt toevoegen, kun je deze hieronder invoeren.</p>
                </div>
            </div>
            <form action="{{route('review.order.store', $orderUuid)}}" method="POST">
                @csrf
                <div class="row mt-70">
                    <div class="col-7 mx-auto text-center">
                        <label style="width:100%">
                            <textarea name="reviewText" id="reviewText" maxlength="500" placeholder="Schrijf hier je review..." required></textarea>
                        </label>
                        <div class="char-counter">
                            <span id="charCount">0</span> / 500 tekens
                        </div>
                    </div>
                </div>
                <div class="d-none">
                    <input name="reviewUuid" type="text" value="{{$review->getUuid()}}">
                </div>
                <div class="row">
                    <div class="col-4 mx-auto mb-100">
                        <div class="btn-wrapper">
                            <button class="btn-review col-12">Plaats review</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewTextarea = document.getElementById('reviewText');
    const charCount = document.getElementById('charCount');

    // Function to update character count
    function updateCharCount() {
        const currentLength = reviewTextarea.value.length;
        charCount.textContent = currentLength;
    }

    // Add event listeners
    if (reviewTextarea) {
        reviewTextarea.addEventListener('input', function() {
            // Real-time validatie van karakters
            const allowedRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]*$/;
            if (this.value && !allowedRegex.test(this.value)) {
                // Filter verboden karakters eruit
                this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ0-9\s\.\,\?\!\:\;\-\–\—\'\"\(\)\@\#\%\&\*\+\=\€\$\/\n\r]/g, '');
            }

            updateCharCount();
        });

        // Initialize character count
        updateCharCount();
    }
});
</script>
@endsection