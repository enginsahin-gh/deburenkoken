@extends('layout.dashboard')

@section('dashboard')
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2">
                <div class="form-box">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($message))
                        <div class="alert alert-warning">
                            {{ $message }}
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('dashboard.dishes') }}" class="btn btn-light btn-orange">Terug naar overzicht</a>
                        </div>
                    @endif

                    <form action="{{ route('dashboard.dishes.store') }}" method="POST" id="form" enctype="multipart/form-data" class="form-box">
                        @csrf

                        {{-- Afbeelding Upload --}}
                        <div class="row">
                            <div class="col-12">
                                <small>Afbeelding gebruik2 (tip: gebruik 1024x1024 pixels)</small>
                            </div>
                            <div class="col-12">
                                <span class="close" onclick="removeImage()">&times;</span>
                                <button id="upload" onclick="openFileSelector();" class="no-style-button">
                                    <span class="add">&times;</span>
                                    <img src="{{ asset('img/defaults/pasta.jpg') }}" class="image gray-background" id="mainImage">
                                    <input class="form-control hide" onchange="fileChange();" type="file" name="profileImage" id="newMainImage">
                                </button>
                            </div>
                        </div>

                        {{-- Naam gerecht --}}
                        <div class="row mt-10">
                            <div class="col-12">
                                <label for="title">Naam gerecht</label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" max="150" required>
                            </div>
                        </div>

                        {{-- Omschrijving --}}
                        <div class="row mt-10">
                            <div class="col-12">
                                <label for="description">Omschrijving (optioneel)</label>
                                <textarea class="form-control" name="description" id="description" maxlength="1000">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- Kenmerken --}}
                        <div class="row mt-10">
                            <div class="col-12">
                                <label>Kenmerken (optioneel)</label>
                                <div class="row"><input type="checkbox" id="spec_vegetarisch" name="specs[]" value="vegetarisch"><label for="spec_vegetarisch">Vegetarisch</label></div>
                                <div class="row"><input type="checkbox" id="spec_glutenvrij" name="specs[]" value="glutenvrij"><label for="spec_glutenvrij">Glutenvrij</label></div>
                                <div class="row"><input type="checkbox" id="spec_veganistisch" name="specs[]" value="veganistisch"><label for="spec_veganistisch">Veganistisch</label></div>
                                <div class="row"><input type="checkbox" id="spec_halal" name="specs[]" value="halal"><label for="spec_halal">Halal</label></div>
                                <div class="row"><input type="checkbox" id="spec_lactosevrij" name="specs[]" value="lactosevrij"><label for="spec_lactosevrij">Lactosevrij</label></div>
                                <div class="row"><input type="checkbox" id="spec_alcohol" name="specs[]" value="bevat alcohol"><label for="spec_alcohol">Bevat alcohol</label></div>
                            </div>
                        </div>

                        {{-- Pittigheid --}}
                        <div class="row mt-10">
                            <div class="col-12">
                                <label>Pittigheid (optioneel)</label>
                                <div class="pepper-rating d-flex align-items-center gap-3 mt-2">
                                    @for ($i = 0; $i <= 3; $i++)
                                        <label onclick="selectSpice({{ $i }})" style="cursor: pointer;">
                                            <input type="radio" id="{{ $i }}-spicy" name="spicy" value="{{ $i }}" style="display: none;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                @if ($i === 0)
                                                    <circle cx="12" cy="12" r="9" fill="#ccc" stroke="#ccc" stroke-width="2"/>
                                                    <line x1="8" y1="8" x2="16" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                    <line x1="16" y1="8" x2="8" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                                @else
                                                    <path d="M7.5 2C7.5 1.17 8.17 0.5 9 0.5C9.83 0.5 10.5 1.17 10.5 2V3.5C10.5 4.33 9.83 5 9 5C8.17 5 7.5 4.33 7.5 3.5V2Z" fill="#ccc"/>
                                                    <path d="M6 6C6 5.17 6.67 4.5 7.5 4.5C8.33 4.5 9 5.17 9 6C9 6.83 8.33 7.5 7.5 7.5C6.67 7.5 6 6.83 6 6Z" fill="#ccc"/>
                                                    <path d="M7.5 8C5.57 8 4 9.57 4 11.5V19C4 20.66 5.34 22 7 22H9C10.66 22 12 20.66 12 19V14.5C12 11.74 9.76 9.5 7 9.5C5.9 9.5 5 8.6 5 7.5C5 6.4 5.9 5.5 7 5.5H7.5C8.33 5.5 9 6.17 9 7V8H7.5Z" fill="#ccc"/>
                                                @endif
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        {{-- Knoppen --}}
                        <div class="row justify-center">
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-light btn-orange">Opslaan</button>
                                <span class="btn btn-light btn-orange" onclick="cancelDish();">Annuleren</span>
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
    const form = document.getElementById('form');
    const newMainImage = document.getElementById('newMainImage');
    const mainImage = document.getElementById('mainImage');
    const defaultImage = '{{ asset('img/defaults/pasta.jpg') }}';
    const reader = new FileReader();

    document.addEventListener('DOMContentLoaded', function () {
        selectSpice(0);
    });

    function selectSpice(level) {
        document.getElementById(level + '-spicy').checked = true;

        for (let i = 0; i <= 3; i++) {
            const icon = document.querySelector(`label[onclick="selectSpice(${i})"] svg`);
            if (i === 0) {
                icon.innerHTML = level === 0 ?
                    '<circle cx="12" cy="12" r="9" fill="#dc3545" stroke="#dc3545" stroke-width="2"/><line x1="8" y1="8" x2="16" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/><line x1="16" y1="8" x2="8" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/>' :
                    '<circle cx="12" cy="12" r="9" fill="#ccc" stroke="#ccc" stroke-width="2"/><line x1="8" y1="8" x2="16" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/><line x1="16" y1="8" x2="8" y2="16" stroke="white" stroke-width="2" stroke-linecap="round"/>';
            } else {
                const color = i <= level ? '#dc3545' : '#ccc';
                icon.innerHTML = `
                    <path d="M7.5 2C7.5 1.17 8.17 0.5 9 0.5C9.83 0.5 10.5 1.17 10.5 2V3.5C10.5 4.33 9.83 5 9 5C8.17 5 7.5 4.33 7.5 3.5V2Z" fill="${color}"/>
                    <path d="M6 6C6 5.17 6.67 4.5 7.5 4.5C8.33 4.5 9 5.17 9 6C9 6.83 8.33 7.5 7.5 7.5C6.67 7.5 6 6.83 6 6Z" fill="${color}"/>
                    <path d="M7.5 8C5.57 8 4 9.57 4 11.5V19C4 20.66 5.34 22 7 22H9C10.66 22 12 20.66 12 19V14.5C12 11.74 9.76 9.5 7 9.5C5.9 9.5 5 8.6 5 7.5C5 6.4 5.9 5.5 7 5.5H7.5C8.33 5.5 9 6.17 9 7V8H7.5Z" fill="${color}"/>
                `;
            }
        }
    }

    function cancelDish() {
        window.location.href = "/dashboard/dishes";
    }

    function openFileSelector() {
        newMainImage.click();
    }

    function removeImage() {
        mainImage.src = defaultImage;
    }

    function fileChange() {
        reader.readAsDataURL(newMainImage.files[0]);
        reader.onload = function (oFREvent) {
            mainImage.src = reader.result;
        };
    }
</script>
@endsection
