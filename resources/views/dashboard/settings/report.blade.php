@extends('layout.dashboard')

@section('dashboard')
    <div class="container mt-10">
        <div class="row">
            <div class="col-8 offset-2">
                <div class="row">
                    <div class="col-12 mt-20">
                        Ontvang een email wanneer:
                    </div>
                </div>
                <form action="{{route('dashboard.settings.reports.update')}}" method="post" class="form-box">
                    @csrf
                    <div class="row mt-3">
                        <div class="col-6">
                            Een klant een bestelling heeft geplaatst.
                        </div>
                        <div class="col-2">
                            <input type="radio" name="new-order" id="new-yes" value="yes" @if(!is_null($cook) && $cook->getMailOrder()) checked @endif @if(is_null($cook)) checked @endif>
                            <label for="new-yes">Ja</label>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="new-order" id="new-no" value="no" @if(!is_null($cook) && !$cook->getMailOrder()) checked @endif>
                            <label for="new-no">Nee</label>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            Een klant een bestelling heeft geannuleerd.
                        </div>
                        <div class="col-2">
                            <input type="radio" name="cancel-order" id="cancel-yes" value="yes" @if(!is_null($cook) && $cook->getMailCancel()) checked @endif @if(is_null($cook)) checked @endif>
                            <label for="cancel-yes">Ja</label>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="cancel-order" id="cancel-no" value="no" @if(!is_null($cook) && !$cook->getMailCancel()) checked @endif>
                            <label for="cancel-no">Nee</label>
                        </div>
                    </div>

                    <!-- <div class="row mt-3">
                        <div class="col-6">
                            ikzelf een advertentie heb geannuleerd
                        </div>
                        <div class="col-2">
                            <input type="radio" name="self-cancel" id="self-yes" value="yes" @if(!is_null($cook) && $cook->getMailSelf()) checked @endif @if(is_null($cook)) checked @endif>
                            <label for="self-yes">Ja</label>
                        </div>
                        <div class="col-2">
                            <input type="radio" name="self-cancel" id="self-no" value="no" @if(!is_null($cook) && !$cook->getMailSelf()) checked @endif>
                            <label for="self-no">Nee</label>
                        </div>
                    </div> -->

                    <div class="row mt-2">
                        <button class="btn btn-light btn-small btn-center" type="submit">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    <script>

    </script>
@endsection

