@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Accounts</h1></div>
    </div>

    <div class="container">
        @if(session('message'))
            <div class="alert alert-success mb-20">
                {{ session('message') }}
            </div>
        @endif

        <!-- Admin Acties -->
        <div class="row mb-20">
            <div class="col-12">
                @if(!is_null($user->getDeletedAt()))
                    {{-- Account is verwijderd - toon alleen herstel knop --}}
                    <a href="{{route('dashboard.admin.accounts.restore', $user->getUuid())}}" class="btn mb-10" onclick="return confirm('Weet je zeker dat je dit account wilt herstellen?')">Account herstellen</a>
                @else
                    {{-- Account is actief of geblokkeerd --}}
                    <a href="{{route('dashboard.admin.accounts.login', $user->getUuid())}}" class="btn mb-10">Log in als Thuiskok</a>
                    @if($user->isBlockedByAdmin())
                        <a href="{{route('dashboard.admin.accounts.unblock', $user->getUuid())}}" class="btn mb-10">Blokkering opheffen</a>
                    @else
                        <a href="{{route('dashboard.admin.accounts.block', $user->getUuid())}}" class="btn btn-white mb-10" onclick="return confirm('Weet je zeker dat je dit account wilt blokkeren?')">Account blokkeren</a>
                    @endif
                    <a href="{{route('dashboard.admin.accounts.delete', $user->getUuid())}}" class="btn btn-white mb-10" style="border-color: #dc3545; color: #dc3545;" onclick="return confirm('Let op! Dit verwijdert het account permanent. Weet je zeker dat je door wilt gaan?')">Account verwijderen</a>
                @endif
            </div>
        </div>

        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="50%">Profielstatus:</td>
                    <td width="50%">@if($user->isBlockedByAdmin()) Geblokkeerd @elseif (!is_null($user->getDeletedAt())) Verwijderd @else Actief @endif</td>
                </tr>
                <tr>
                    <td>Profielnaam:</td>
                    <td>{{$user->getUsername()}}</td>
                </tr>
                <tr>
                    <td>E-mailadres:</td>
                    <td>{{ $user->getEmail() }}</td>
                </tr>
                <tr>
                    <td>Account aangemaakt op:</td>
                    <td>{{$user->getCreatedAt()->translatedFormat('d-m-Y')}}</td>
                </tr>
                <tr>
                    <td>Reviewscore:</td>
                    <td>{{$user->reviews->avg('rating') ?? 0}}</td>
                </tr>
                <tr>
                    <td>Laatst aangemeld:</td>
                    <td>{{$user->getUpdatedAt()}}</td>
                </tr>
                <tr>
                    <td>Aantal gerechten aangemaakt:</td>
                    <td>{{$user->dish->count()}}</td>
                </tr>
                <tr>
                    <td>Aantal advertenties aangemaakt:</td>
                    <td><?php $adverts = 0;
                    foreach ($user->dish as $dish) {
                        $adverts += $dish->adverts->count();
                    } ?> {{$adverts}}</td>
                </tr>
                <tr>
                    <td>Aantal advertenties op dit moment online:</td>
                    <td>{{$user->cook?->adverts->whereNotNull('published')->where('pickup_date', '>=', \Carbon\Carbon::now()->format('Y-m-d'))->count() ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Aantal euro aan bestellingen verkocht:</td>
                    <td>€{{ number_format($user->wallet?->walletLines->whereIn('state', [1, 2, 3, 4, 7])->sum('amount') ?? 0, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Aantal keer advertentie gewijzigd:</td>
                    <td>{{$user->cook?->adverts()->whereColumn('adverts.updated_at', '>', 'adverts.created_at')->count() ?? 0}}</td>
                </tr>
                <tr>
                    <td>Aantal keer advertentie geannuleerd:</td>
                    <td>{{ $user->cook?->adverts()->onlyTrashed()->count() ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Aantal klanten die zich aangemeld hebben om een mail te ontvangen:</td>
                    <td>{{ $user->cook?->mailingList?->count() ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Type Thuiskok:</td>
                    <td>{{$user->type_thuiskok ?? 'Particuliere Thuiskok'}}</td>
                </tr>
            </tbody>
        </table>

        <!-- KVK Gegevens Bewerken -->
        <div class="mt-30 mb-30">
            <h3 class="mb-20">KVK Gegevens</h3>
            <form id="kvk-form" action="{{ route('dashboard.admin.accounts.kvk.update', $user->getUuid()) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6 mb-15">
                        <label for="kvk_naam">KVK Naam</label>
                        <input type="text" class="form-control @error('kvk_naam') is-invalid @enderror"
                               id="kvk_naam" name="kvk_naam"
                               value="{{ old('kvk_naam', $user->kvk_naam ?? '') }}">
                        @error('kvk_naam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-15">
                        <label for="kvk_nummer">KVK Nummer</label>
                        <input type="text" class="form-control @error('kvk_nummer') is-invalid @enderror"
                               id="kvk_nummer" name="kvk_nummer"
                               value="{{ old('kvk_nummer', $user->kvk_nummer ?? '') }}">
                        @error('kvk_nummer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-15">
                        <label for="btw_nummer">BTW Nummer</label>
                        <input type="text" class="form-control @error('btw_nummer') is-invalid @enderror"
                               id="btw_nummer" name="btw_nummer"
                               value="{{ old('btw_nummer', $user->btw_nummer ?? '') }}">
                        @error('btw_nummer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-15">
                        <label for="rsin">RSIN</label>
                        <input type="text" class="form-control @error('rsin') is-invalid @enderror"
                               id="rsin" name="rsin"
                               value="{{ old('rsin', $user->rsin ?? '') }}">
                        @error('rsin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-15">
                        <label for="vestigingsnummer">Vestigingsnummer</label>
                        <input type="text" class="form-control @error('vestigingsnummer') is-invalid @enderror"
                               id="vestigingsnummer" name="vestigingsnummer"
                               value="{{ old('vestigingsnummer', $user->vestigingsnummer ?? '') }}">
                        @error('vestigingsnummer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-15">
                        <label for="nvwa_nummer">NVWA Nummer</label>
                        <input type="text" class="form-control @error('nvwa_nummer') is-invalid @enderror"
                               id="nvwa_nummer" name="nvwa_nummer"
                               value="{{ old('nvwa_nummer', $user->nvwa_nummer ?? '') }}">
                        @error('nvwa_nummer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mt-10">
                    <div class="col-12">
                        <button type="submit" class="btn">KVK gegevens opslaan</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <small class="text-muted">Let op: bij invullen van KVK naam of nummer wordt dit automatisch een Zakelijke Thuiskok.</small>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection


