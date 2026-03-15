@extends('layout.dashboard')

@section('dashboard')
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2">

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
                            <a href="{{ route('dashboard.dishes.new') }}" class="btn btn-light btn-orange">Terug naar overzicht</a>
                        </div>
                    @else
                        @if($edit && $dish)
                            <form action="{{ route('dashboard.dishes.update', $dish->getUuid()) }}" method="POST" id="form" enctype="multipart/form-data" class="form-box">
                                @csrf
                                @method('PATCH')
                        @else
                            <form action="{{route('dashboard.dishes.store')}}" method="POST" id="form" enctype="multipart/form-data" class="form-box">
                                @csrf
                        @endif
                            <div class="row">
                                <div class="col-12">
                                    <small>Afbeelding gebruik (tip: gebruik 1024x1024 pixels)</small>
                                </div>
                                <div class="col-12">
                                    <div class="upload-container">
                                        <span class="close" id="removeBtn" onclick="removeImage()" style="display: none;">&times;</span>
                                        <button id="upload" onclick="openFileSelector();" type="button" class="no-style-button upload-button">
                                            <span class="add">&times;</span>
                                            @if($edit && $dish && $dish->image)
                                                <img src="{{ $dish->image->getCompletePath() }}" class="image" id="mainImage" style="width: 150px; height: 150px; object-fit: cover;">
                                            @else
                                                <img src="{{ url('/img/pasta.jpg') }}" class="image gray-background" id="mainImage" style="width: 150px; height: 150px; object-fit: cover;">
                                            @endif
                                            <div class="upload-overlay">
                                                <i class="fa fa-camera" style="font-size: 24px; color: white;"></i>
                                                <p style="margin: 5px 0 0 0; color: white; font-size: 12px;">Klik om te wijzigen</p>
                                            </div>
                                            <input class="form-control hide" onchange="fileChange();" type="file" name="profileImage" id="newMainImage" accept="image/*">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-12">
                                    <label>Naam gerecht</label>
                                    @if($edit && $dish)
                                        <p class="form-control-plaintext"><strong>{{ $dish->getTitle() }}</strong></p>
                                        <small class="text-muted">De naam van het gerecht kan niet worden aangepast.</small>
                                    @else
                                        <input type="text" id="title" name="title" class="form-control" value="{{old('title')}}" max="150" required>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-12">
                                    <label>
                                        Prijs per portie (€)
                                    </label>
                                    @if($edit && $dish)
                                        <p class="form-control-plaintext"><strong>€{{ number_format($dish->getPortionPrice(), 2, ',', '.') }}</strong></p>
                                        <small class="text-muted">De prijs kan niet worden aangepast.</small>
                                    @else
                                        <label for="price">
                                            Prijs per portie (€)
                                            <span class="tooltip">
                                                <i class="fa fa-info-circle"></i>
                                                 <span class="tooltiptext">Prijs is inclusief BTW (tussen €1,00 en €25,00)</span>
                                            </span>
                                        </label>
                                        <input type="number" step="0.01" id="price" name="price" min="1.00" max="25" value="{{old('price')}}" class="form-control" required>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-12">
                                    <label for="description">Omschrijving (optioneel)</label>
                                    <textarea class="form-control" placeholder="Beschrijf je gerecht zo gedetailleerd mogelijk..." name="description" id="description" maxlength="1000">{{ $edit && $dish ? old('description', $dish->getDescription()) : old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-12">
                                    <label>Kenmerken (optioneel)</label>
                                    @php
                                        $specs = ['vegetarisch', 'glutenvrij', 'veganistisch', 'halal', 'lactosevrij', 'bevat alcohol'];
                                        $specMap = [
                                            'vegetarisch' => $edit && $dish ? $dish->isVegetarian() : false,
                                            'glutenvrij' => $edit && $dish ? $dish->hasGluten() : false,
                                            'veganistisch' => $edit && $dish ? $dish->isVegan() : false,
                                            'halal' => $edit && $dish ? $dish->isHalal() : false,
                                            'lactosevrij' => $edit && $dish ? $dish->hasLactose() : false,
                                            'bevat alcohol' => $edit && $dish ? $dish->hasAlcohol() : false,
                                        ];
                                    @endphp
                                    @if($edit && $dish)
                                        @foreach ($specs as $spec)
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="checkbox" id="spec_{{ $spec }}" name="specs[]" value="{{ $spec }}" {{ $specMap[$spec] ? 'checked' : '' }}>
                                                <label for="spec_{{ $spec }}" class="checkbox-inline">{{ ucfirst($spec) }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                        <input type="hidden" name="vegetarian" id="vegetarian_hidden" value="{{ $dish->isVegetarian() ? '1' : '0' }}">
                                        <input type="hidden" name="vegan" id="vegan_hidden" value="{{ $dish->isVegan() ? '1' : '0' }}">
                                        <input type="hidden" name="halal" id="halal_hidden" value="{{ $dish->isHalal() ? '1' : '0' }}">
                                        <input type="hidden" name="alcohol" id="alcohol_hidden" value="{{ $dish->hasAlcohol() ? '1' : '0' }}">
                                        <input type="hidden" name="gluten" id="gluten_hidden" value="{{ $dish->hasGluten() ? '1' : '0' }}">
                                        <input type="hidden" name="lactose" id="lactose_hidden" value="{{ $dish->hasLactose() ? '1' : '0' }}">
                                    @else
                                        @foreach ($specs as $spec)
                                        <div class="row">
                                            <div class="col-12">
                                                <input type="checkbox" id="spec_{{ $spec }}" name="specs[]" value="{{ $spec }}">
                                                <label for="spec_{{ $spec }}" class="checkbox-inline">{{ ucfirst($spec) }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-10">
                                <div class="col-12">
                                    <label>Pittigheid (optioneel)</label>
                                   <div id="spice-level-selector" style="display: flex; gap: 10px; font-size: 24px; cursor: pointer;">
                                        <i class="fa-solid fa-xmark" id="pepper-0" onclick="selectSpice(0)" style="color: #ccc; font-size: 22px;"></i>
                                        @for($i = 1; $i <= 3; $i++)
                                            <i class="fa-solid fa-pepper-hot" id="pepper-{{ $i }}" onclick="selectSpice({{ $i }})" style="color: #ccc;"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="spicy" id="spicy" value="{{ $edit && $dish ? $dish->getSpiceLevel() : 0 }}">
                                </div>
                            </div>

                            <div class="row justify-center">
                                <div class="text-center mt-5">
                                    <span class="btn btn-light btn-orange" onclick="addDish();">Opslaan</span>
                                    <span class="btn btn-light btn-orange" onclick="cancelDish();">Annuleren</span>
                                </div>
                            </div>
                        </form>
                    @endif

            </div>
        </div>
    </div>

    <style>
        .upload-container {
            position: relative;
            display: inline-block;
        }

        .upload-button {
            position: relative;
            cursor: pointer !important;
            border: 2px dashed #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .upload-button:hover {
            border-color: #007bff;
            transform: scale(1.02);
        }

        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 6px;
        }

        .upload-button:hover .upload-overlay {
            opacity: 1;
        }

        .upload-button img {
            border-radius: 6px;
        }

        .close {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            z-index: 20;
            border: 2px solid white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        /* TOOLTIP STYLING */
        .fa-solid,
        .fa-circle-info,
        .fa-info-circle,
        i[class*="fa-"] {
            text-decoration: none !important;
            border: none !important;
            border-bottom: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .fa-solid::before,
        .fa-solid::after,
        .fa-circle-info::before,
        .fa-circle-info::after,
        .fa-info-circle::before,
        .fa-info-circle::after,
        i[class*="fa-"]::before,
        i[class*="fa-"]::after {
            border: none !important;
            border-bottom: none !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .tooltip,
        .tooltip *,
        .ml-2,
        .ml-2 * {
            text-decoration: none !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }

        .tooltip {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 300px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 10px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .tooltip:hover .tooltiptext::after {
            visibility: visible;
            opacity: 1;
        }

        @media (max-width: 767px) {
            .tooltip {
                margin-left: 5px !important;
                vertical-align: middle !important;
            }

            .tooltip .tooltiptext {
                width: 250px !important;
                max-width: 90vw !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
                bottom: 130% !important;
                font-size: 14px !important;
            }
        }
    </style>
@endsection

@section('page.scripts')
<script>
    const newMainImage = document.getElementById('newMainImage');
    const mainImage = document.getElementById('mainImage');
    const removeBtn = document.getElementById('removeBtn');
    const defaultImage = '{{ url('/img/pasta.jpg') }}';
    const reader = new FileReader();
    const form = document.getElementById('form');

    @if($edit && $dish)
    // Sync checkbox state to hidden inputs on change
    const specCheckboxMap = {
        'vegetarisch': 'vegetarian_hidden',
        'glutenvrij': 'gluten_hidden',
        'veganistisch': 'vegan_hidden',
        'halal': 'halal_hidden',
        'lactosevrij': 'lactose_hidden',
        'bevat alcohol': 'alcohol_hidden',
    };

    document.querySelectorAll('input[name="specs[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const hiddenId = specCheckboxMap[this.value];
            if (hiddenId) {
                document.getElementById(hiddenId).value = this.checked ? '1' : '0';
            }
        });
    });
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        const currentLevel = parseInt(document.getElementById('spicy').value || '0');
        selectSpice(currentLevel);
        checkImageState();

        const tooltipIcons = document.querySelectorAll('.tooltip i');
        tooltipIcons.forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            icon.addEventListener('touchend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });
        });
    });

    function selectSpice(level) {
        document.getElementById('spicy').value = level;

        for (let i = 0; i <= 3; i++) {
            const icon = document.getElementById(`pepper-${i}`);
            if (!icon) continue;
            icon.style.color = (i === 0 && level === 0) || (i !== 0 && i <= level) ? '#dc3545' : '#ccc';
        }
    }

    function cancelDish() {
        window.location.href = "/dashboard/dishes";
    }

    function openFileSelector() {
        if (newMainImage) {
            newMainImage.click();
        }
    }

    function removeImage() {
        mainImage.src = defaultImage;
        newMainImage.value = "";
        checkImageState();
    }

    function fileChange() {
        if (newMainImage.files && newMainImage.files[0]) {
            reader.readAsDataURL(newMainImage.files[0]);
            reader.onload = function (e) {
                mainImage.src = e.target.result;
                checkImageState();
            }
        }
    }

    function checkImageState() {
        if (mainImage.src !== defaultImage && !mainImage.src.includes('/img/pasta.jpg')) {
            removeBtn.style.display = 'flex';
        } else {
            removeBtn.style.display = 'none';
        }
    }

    function addDish() {
        form.submit();
    }
</script>
@endsection
