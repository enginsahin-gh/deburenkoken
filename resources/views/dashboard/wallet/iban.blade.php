@extends('layout.dashboard')

@section('dashboard')

    <div class="page-header neg-header">
        <div class="container"><h1>IBAN</h1></div>
    </div>
    <div class="container mt-20">
      

        @if (isset($verification) && $verification)
            <div class="row justify-content-center">
                <div class="col-6">
                    <p>Een bankrekening moet toegevoegd zijn voordat je een advertentie online kunt plaatsen.</p>
                </div>
            </div>
        @endif

        @if (isset($verify) && $verify)
            <div class="row justify-content-center">
                <div class="col-6">
                    <p>IBAN is nog niet gevalideerd. Wacht geduldig af.</p>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-6">
                @if ($iban && $iban->getIban())
                    <a href="#" onclick="showModal()" class="btn btn-small btn-light">IBAN aanpassen</a>
                @else
                    <a href="#" onclick="showModal()" class="btn btn-small btn-light">IBAN toevoegen</a>
                @endif
            </div>
        </div>

        @if($iban && $iban->getIban())
            <div class="row">
                <div class="col-6">
                    <label for="account_holder">Voor en achternaam van rekeninghouder<span class="required-star"></span></label>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <input id="account_holder" class="form-control" value="{{ $iban->getAccountHolder() }}" disabled>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="iban">Bankrekening (IBAN)<span class="required-star"></span></label>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <input type="text" class="form-control" id="iban" value="{{ strlen($iban->getIban()) > 3 ? str_repeat('*', max(0, strlen($iban->getIban()) - 3)) . substr($iban->getIban(), -3) : $iban->getIban() }}" disabled>
                </div>
            </div>
           
        @endif
    </div>

    @if ($errors->any() || session('errorMessage'))
        <script>
            window.onload = function () {
                showModal();
            }
        </script>
    @endif

    <div class="modal" id="modal">
        <div class="form">
            <div class="row" id="form">
                <div class="col-12 text-center">
                    <span class="close" id="closeModal" onclick="closeModal();">&times;</span>
                    <div class="row mt-20">
                        <div class="col-12">
                            <h3>Gegevens IBAN</h3>
                        </div>
                    </div>

                    <form action="{{ route('dashboard.wallet.iban.add') }}" method="post">
                        @csrf
                        <small>Je wordt doorgestuurd naar een betalingspagina waar je 1 cent betaalt ter verificatie van je bankrekening.</small>
                        <input type="hidden" name="type" value="true">
                        <button type="submit" style='display: block;' class="btn btn-small btn-light">IBAN verifiëren</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="id-modal">
        <div class="form">
            <div class="row" id="form">
                <div class="col-12">
                    <span class="close" id="closeIdModal" onclick="closeIdModal();">&times;</span>
                    <div class="row mt-20">
                        <div class="col-12">
                            <h3>Identiteit verificatie</h3>
                        </div>
                    </div>

                    <form action="{{ route('dashboard.wallet.iban.id') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <label for="id-card">Kopie identiteitsbewijs<span class="required-star">*</span></label>
                                <input type="file" id="id-card" class="form-control" name="id-card" required>
                            </div>
                        </div>
                        @if($errors->has('id-card'))
                            <span class="alert-warning">{{ $errors->first('id-card') }}</span>
                        @endif
                        <div class="row mt-20 mb-20">
                            <div class="col-12 text-center">
                                <a href="#" onclick="closeIdModal()" class="btn btn-small btn-outline-fat mr-20">Annuleer</a>
                                <button type="submit" class="btn btn-small btn-light">Uploaden</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    <script>
        const modal = document.getElementById('modal');
        const close = document.getElementById('closeModal');
        const idModal = document.getElementById('id-modal');
        const idClose = document.getElementById('closeIdModal');

        function showModal() {
            modal.style.display = 'block';
            close.style.display = 'block';
        }

        function showIdModal() {
            idModal.style.display = 'block';
            idClose.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
            close.style.display = 'none';
        }

        function closeIdModal() {
            idModal.style.display = 'none';
            idClose.style.display = 'none';
        }
    </script>
@endsection