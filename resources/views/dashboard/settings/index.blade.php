@extends('layout.dashboard')

@section('dashboard')
    <style>
        @media (max-width: 768px) {
            .remarks-counter {
                position: relative;
                top: 20%;
                left: 93%;
                z-index: 10;
                transform: translateY(-230%);
            }

            .table-row {
                position: relative;
            }
        }

        @media (max-width: 768px) {
            .introjs-tooltip {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 90% !important;
                max-width: 90vw !important;
                text-align: center !important;
                z-index: 9999 !important;
                margin: 0 auto !important;
            }

            .introjs-tooltip-buttons {
                position: relative !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 100% !important;
                background-color: rgba(255, 255, 255, 0.9) !important;
                padding: 10px 0 !important;
                display: flex !important;
                justify-content: center !important;
            }

            body {
                padding-top: 50px;
            }

            .justify-center {
                justify-content: center !important;
            }
        }

        /* Profile Image Upload Styling */
        .profile-upload-container {
            position: relative;
            display: inline-block;
        }

        .profile-upload-button {
            position: relative;
            background: none;
            border: none;
            padding: 0;
            cursor: default;
            display: block;
            border-radius: 6px;
            overflow: hidden;
        }

        .profile-upload-button.edit-mode {
            cursor: pointer !important;
            border: 2px dashed #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .profile-upload-button.edit-mode:hover {
            border-color: #007bff;
            transform: scale(1.02);
        }

        .profile-upload-button.blocked {
            cursor: not-allowed !important;
            border-color: #dc3545 !important;
            opacity: 0.6;
        }

        .profile-upload-overlay {
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
            pointer-events: none;
        }

        .profile-upload-button.edit-mode .profile-upload-overlay {
            pointer-events: all;
        }

        .profile-upload-button.edit-mode:hover .profile-upload-overlay {
            opacity: 1;
        }

        .profile-upload-button.blocked .profile-upload-overlay {
            background: rgba(220, 53, 69, 0.7) !important;
        }

        .profile-upload-button img {
            border-radius: 6px;
            display: block;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .profile-close {
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
            font-weight: bold;
        }

        .profile-close:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .profile-close:active {
            transform: scale(0.95);
        }

        /* Visual feedback for default image state */
        .profile-upload-button.edit-mode .profile-upload-overlay i {
            font-size: 24px;
            color: white;
            margin-bottom: 5px;
        }

        .profile-upload-button.edit-mode .profile-upload-overlay p {
            margin: 0;
            color: white;
            font-size: 12px;
            text-align: center;
        }

        .profile-upload-button.blocked .profile-upload-overlay p {
            color: white;
            font-size: 11px;
            text-align: center;
        }

        /* Loading state */
        .profile-upload-button.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .profile-upload-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #ccc;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* RONDE STYLING VOOR ONDERSTE RIJ - 75px (helft kleiner) */
        .profile-image-item {
            position: relative;
            display: inline-block;
            margin: 10px;
        }

        .profile-image-item img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .profile-image-item .close {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            z-index: 10;
            cursor: pointer;
            border: 2px solid white;
            font-weight: bold;
        }

        .profile-image-item .close:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        /* Ronde lege slots - 75px */
        .empty-slot {
            display: inline-block;
            width: 75px;
            height: 75px;
            border: 2px dashed #ccc;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            position: relative;
            background-color: #f8f9fa;
        }

        .empty-slot:hover {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
        }

        .empty-slot.disabled {
            cursor: not-allowed;
            opacity: 0.4;
            border-color: #e9ecef;
            background-color: #f8f9fa;
        }

        .empty-slot.disabled:hover {
            border-color: #e9ecef;
            background-color: #f8f9fa;
        }

        .empty-slot .add {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: #6c757d;
            font-weight: bold;
        }

        .empty-slot:hover .add {
            color: #007bff;
        }

        .empty-slot.disabled .add {
            color: #adb5bd;
        }

        /* Readonly username styling */
        #username[readonly] {
            background-color: #e9ecef;
            color: #495057;
            cursor: not-allowed;
            border: 1px solid #ced4da;
            opacity: 1;
        }

        /* Success melding */
        .save-success {
            display: none;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 8px 16px;
            margin-top: 10px;
            font-size: 14px;
            transition: opacity 0.5s ease;
        }

        .save-success.visible {
            display: inline-block;
        }

        .save-success.fade-out {
            opacity: 0;
        }

        /* Username helptekst */
        .username-help {
            color: #6c757d;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
    <div class="container mt-30">
        <div class="row">
            <div class="col-8 offset-2">
                <div class="row">
                    <div class="col-8 offset-2 page-title">
                        <h2 class="m-0">Profiel</h2>
                    </div>
                </div>
                <div class="form-box">
                    <div class="row justify-center">
                        <div class="img-wrap ml-40" style='margin-left: 0px;'>
                            <div class="profile-upload-container">
                                <button type="button" class="profile-close" id="profileRemoveBtn"
                                    onclick="removeProfileImage()" aria-label="Profielfoto verwijderen"
                                    style="display: none;">&times;</button>
                                <button type="button" id="upload" class="no-style-button profile-upload-button edit-mode"
                                    onclick="openMainImageUpload();" aria-label="Profielfoto wijzigen">
                                    <img id="uploadImg"
                                        src="{{ (!empty($profileImagePath) && $profileImagePath !== '') ? $profileImagePath : url('/img/kok.png') }}"
                                        style='height: 150px; width: 150px; object-fit: cover;'
                                        class="image gray-background" data-id="{{$mainProfileImage?->getUuid() ?? 'none'}}"
                                        alt="Profielfoto">
                                    <div class="profile-upload-overlay" id="profileOverlay" style="display: flex;">
                                        <i class="fa fa-camera" style="font-size: 24px; color: white;"
                                            aria-hidden="true"></i>
                                        <p id="overlayText" style="margin: 5px 0 0 0; color: white; font-size: 12px;">Klik
                                            om te wijzigen</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Upload Modal -->
                        <div id="profile-upload" class="profile-upload" role="dialog" aria-modal="true"
                            aria-labelledby="uploadModalTitle">
                            <div class="modal-content">
                                <h3 id="uploadModalTitle" class="sr-only">Afbeelding uploaden</h3>
                                <button type="button" class="close" id="closeModal" onclick="closeModal();"
                                    aria-label="Sluiten">&times;</button>
                                @if($errors->any())
                                    {!! implode('', $errors->all('<span class="error">:message</span>')) !!}
                                @endif
                                <div id='uploadForm'>
                                    @csrf
                                    <input type="hidden" id="old_uuid" name="old_uuid"
                                        value="{{$mainProfileImage?->getUuid() ?? ''}}">
                                    <input type="hidden" name="user_uuid" value="{{$user->getUuid()}}">
                                    <input type="hidden" name="upload_type" value="main" id="upload_type">
                                    <div class="form-group">
                                        <label for="profileImage">Selecteer afbeelding (Voeg alleen een afbeelding toe met
                                            het bestandstype: JPEG, PNG, JFIF, GIF, WBMP of GD.)</label>
                                        <input class="form-control" style="padding-bottom: 36px;" type="file"
                                            name="profileImage" id="profileImage" accept="image/*" required>
                                        <p id='uploadError' class="text-danger" role="alert"></p>
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-small btn-light" onclick="uploadImage()" type='button'>Upload
                                            foto</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="username">Thuiskok naam</label>
                            <input type="text" id="username" value="{{$user->getUsername()}}" readonly aria-readonly="true"
                                aria-describedby="username-help">
                            <small id="username-help" class="username-help">De thuiskok naam kan niet worden gewijzigd</small>
                        </div>
                    </div>

                    <!-- DESCRIPTION FORM -->
                    <div id="description-container">
                        @csrf
                        <div class="row position-relative mt-10">
                            <div class="col-12">
                                <label for="profile-description">Omschrijving Thuiskok (optioneel)</label>
                                <textarea oninput="countInput()" placeholder="Geen omschrijving" name="profile-description"
                                    id="profile-description" maxlength="1000">{{ $profileDescription }}</textarea>
                                @error('profile-description')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                                <div class="char-counter" aria-live="polite" aria-atomic="true">
                                    <span id="current">{{ strlen($profileDescription) }}</span>
                                    <span id="maximum">/1000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ALTIJD 3 RONDE SLOTS ZICHTBAAR -->
                    <div id='emptyImages' class="row mt-10 justify-center" style="display: flex !important;">
                        <div class="col-12 text-center">
                            @php $slotCount = 0; @endphp

                            @foreach($profileImages as $profileImage)
                                <div class="profile-image-item" id="item-{{ $profileImage->getUuid() }}">
                                    <button type="button" onclick="removeAdditionalImage('{{$profileImage->getUuid()}}', this);"
                                        class="close" aria-label="Verwijder afbeelding">×</button>
                                    <img src="{{$profileImage->getCompletePath()}}" alt="{{$profileImage->getDescription()}}">
                                </div>
                                @php $slotCount++; @endphp
                            @endforeach

                            @for($i = $slotCount; $i < 3; $i++)
                                <div class="empty-slot" id="empty-slot-{{ $i }}" role="button" tabindex="0"
                                    aria-label="Extra foto toevoegen" onclick="openAdditionalImageUpload()"
                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openAdditionalImageUpload();}">
                                    <span class="add" aria-hidden="true">+</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="row mt-40 justify-center text-center" style="margin-top: 15px !Important;">
                        <div class="col-12">
                            <button type="button" id="saveButton" onclick="saveProfileDescription()"
                                class="btn btn-light btn-orange">Opslaan</button>
                            <div id="save-success" class="save-success" role="status" aria-live="polite">&#10003;
                                Opgeslagen!</div>
                        </div>
                    </div>
                </div>
                <div class="form-box">
                    <div class="row">
                        <div class='col text-center'>
                            @if ($deleteAccount)
                                <p class='p-0 m-0'>Wil je je account verwijderen? Klik dan op de onderstaande knop.</p>
                                <a href="{{route('dashboard.settings.profile.remove')}}" class="btn btn-small btn-outline-fat"
                                    style="margin-bottom: 5px !important;">Account verwijderen</a>
                            @else
                                <p class='p-0 m-0 text-danger'>Je account kan niet verwijdert worden als je nog saldo
                                    beschikbaar hebt staan </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

    @section('page.scripts')
        <script>
            const modal = document.getElementById('profile-upload');
            const modalUuid = document.getElementById('old_uuid');
            const textArea = document.getElementById('profile-description');
            const uploadTypeInput = document.getElementById('upload_type');
            const profileRemoveBtn = document.getElementById('profileRemoveBtn');
            const defaultProfileImage = '{{ url('/img/kok.png') }}';
            const overlayText = document.getElementById('overlayText');
            const saveSuccessEl = document.getElementById('save-success');

            let editMode = true;
            let currentProfileImageUuid = '{{$mainProfileImage?->getUuid() ?? ''}}';
            let currentAdditionalCount = <?php echo $profileImages->count() ?>;
            let mainImageUploadPending = false;
            let uploadImg, uploadButton, uploadOverlay;

            document.addEventListener('DOMContentLoaded', function () {
                uploadImg = document.getElementById('uploadImg');
                uploadButton = document.getElementById('upload');
                uploadOverlay = document.getElementById('profileOverlay');

                if (uploadImg) {
                    uploadImg.onerror = function () {
                        this.src = defaultProfileImage;
                        this.setAttribute('data-id', 'none');
                        currentProfileImageUuid = '';
                        checkProfileImageState();
                    };

                    if (uploadImg.src === '' || uploadImg.src === window.location.href) {
                        uploadImg.src = defaultProfileImage;
                        uploadImg.setAttribute('data-id', 'none');
                        currentProfileImageUuid = '';
                    }
                }

                checkProfileImageState();
                setEditMode(true);
                updateEmptySlots();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && modal && modal.style.display === 'block') {
                    closeModal();
                }
            });

            function showSaveSuccess() {
                if (!saveSuccessEl) return;
                saveSuccessEl.classList.remove('fade-out');
                saveSuccessEl.classList.add('visible');
                setTimeout(function () {
                    saveSuccessEl.classList.add('fade-out');
                    setTimeout(function () {
                        saveSuccessEl.classList.remove('visible', 'fade-out');
                    }, 500);
                }, 3000);
            }

            function saveProfileDescription() {
                const description = textArea.value;

                $.ajax({
                    url: '{{ route("dashboard.settings.profile.description.post") }}',
                    type: 'POST',
                    data: {
                        'profile-description': description,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        showSaveSuccess();
                    },
                    error: function (xhr, status, error) {
                        alert('Fout bij het opslaan van de omschrijving. Probeer het opnieuw.');
                    }
                });
            }

            function setEditMode(enabled) {
                if (!uploadButton || !uploadOverlay) return;

                editMode = enabled;

                if (enabled) {
                    uploadButton.classList.add('edit-mode');
                    uploadOverlay.style.display = 'flex';

                    if (mainImageUploadPending) {
                        uploadButton.classList.add('blocked');
                        overlayText.textContent = 'Klik eerst "Opslaan"';
                    } else {
                        uploadButton.classList.remove('blocked');
                        overlayText.textContent = 'Klik om te wijzigen';
                    }
                } else {
                    uploadButton.classList.remove('edit-mode', 'blocked');
                    uploadOverlay.style.display = 'none';
                    mainImageUploadPending = false;
                }

                checkProfileImageState();
                updateEmptySlots();
                updateCloseButtons();
            }

            function isDefaultImage() {
                if (!uploadImg) return true;

                const currentSrc = uploadImg.src;
                return currentSrc === defaultProfileImage ||
                    currentSrc.includes('/img/kok.png') ||
                    currentSrc === '' ||
                    currentSrc === window.location.href ||
                    uploadImg.getAttribute('data-id') === 'none' ||
                    !currentProfileImageUuid ||
                    currentProfileImageUuid === '';
            }

            function checkProfileImageState() {
                if (!uploadImg || !profileRemoveBtn) return;

                const isDefault = isDefaultImage();

                if (!isDefault) {
                    profileRemoveBtn.style.display = 'flex';
                } else {
                    profileRemoveBtn.style.display = 'none';
                }
            }

            function updateCloseButtons() {
                const closeButtons = document.querySelectorAll('.profile-image-item .close');
                closeButtons.forEach(btn => {
                    btn.style.display = 'flex';
                });
            }

            function updateEmptySlots() {
                const emptySlots = document.querySelectorAll('.empty-slot');

                emptySlots.forEach((slot, index) => {
                    const addButton = slot.querySelector('.add');

                    if (currentAdditionalCount < 3) {
                        slot.classList.remove('disabled');
                        slot.setAttribute('tabindex', '0');
                        if (addButton) addButton.style.display = 'block';
                    } else {
                        slot.classList.add('disabled');
                        slot.setAttribute('tabindex', '-1');
                        if (addButton) addButton.style.display = 'none';
                    }
                });
            }

            function openMainImageUpload() {
                if (mainImageUploadPending) {
                    alert('Klik eerst op "Opslaan" voordat je een nieuwe afbeelding uploadt.');
                    return;
                }

                uploadTypeInput.value = 'main';
                modal.style.display = 'block';
                if (modalUuid) modalUuid.value = currentProfileImageUuid || '';
            }

            function removeProfileImage() {
                if (currentProfileImageUuid && currentProfileImageUuid !== '') {
                    $.ajax({
                        url: '{{ route("dashboard.settings.profile.image.delete") }}',
                        type: 'DELETE',
                        data: {
                            old_uuid: currentProfileImageUuid,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            resetToDefaultImage();
                        },
                        error: function (xhr, status, error) {
                            alert('Fout bij het verwijderen van de profielfoto. Probeer het opnieuw.');
                        }
                    });
                } else {
                    resetToDefaultImage();
                }
            }

            function resetToDefaultImage() {
                currentProfileImageUuid = '';
                mainImageUploadPending = false;

                if (modalUuid) modalUuid.value = '';

                if (uploadImg) {
                    uploadImg.src = defaultProfileImage;
                    uploadImg.setAttribute('data-id', 'none');

                    setTimeout(function () {
                        uploadImg.src = defaultProfileImage + '?t=' + Date.now();

                        uploadImg.onerror = function () {
                            this.src = defaultProfileImage;
                            this.setAttribute('data-id', 'none');
                            currentProfileImageUuid = '';
                            checkProfileImageState();
                        };
                    }, 50);
                }

                checkProfileImageState();
                setEditMode(editMode);
            }

            function openAdditionalImageUpload() {
                if (currentAdditionalCount >= 3) return;

                uploadTypeInput.value = 'additional';
                modal.style.display = 'block';
                if (modalUuid) modalUuid.value = '';
            }

            function removeAdditionalImage(imageUuid, button) {
                $.ajax({
                    url: '{{ route("dashboard.settings.profile.image.delete") }}',
                    type: 'DELETE',
                    data: {
                        old_uuid: imageUuid,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        const itemElement = document.getElementById('item-' + imageUuid);
                        if (itemElement) {
                            itemElement.replaceWith(createEmptySlot());
                        }

                        currentAdditionalCount--;
                        updateEmptySlots();
                    },
                    error: function (xhr, status, error) {
                        alert('Fout bij het verwijderen van de afbeelding. Probeer het opnieuw.');
                    }
                });
            }

            function closeModal() {
                modal.style.display = 'none';

                const profileImageInput = document.getElementById('profileImage');
                const uploadError = document.getElementById('uploadError');

                if (profileImageInput) profileImageInput.value = '';
                if (uploadError) uploadError.innerText = '';
            }

            window.onclick = function (event) {
                if (event.target === modal) {
                    closeModal();
                }
            }

            function countInput() {
                const currentLength = textArea.value.length;
                document.getElementById('current').innerText = currentLength;
            }

            function uploadImage() {
                const profileImageInput = document.getElementById('profileImage');
                const uploadError = document.getElementById('uploadError');

                if (!profileImageInput || !uploadError) {
                    return;
                }

                const file = profileImageInput.files[0];
                if (!file) {
                    uploadError.innerText = 'Selecteer eerst een afbeelding';
                    return;
                }

                const formData = new FormData();
                formData.append('profileImage', file);
                formData.append('upload_type', uploadTypeInput.value);
                formData.append('_token', '{{ csrf_token() }}');

                uploadError.innerText = '';

                $.ajax({
                    url: '{{ route("dashboard.settings.profile.image") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.upload_type === 'main') {
                            currentProfileImageUuid = response.image.uuid;
                            mainImageUploadPending = true;

                            if (uploadImg) {
                                const newImageUrl = '/' + response.image.path + '/' + response.image.name;

                                uploadImg.src = newImageUrl + '?v=' + Date.now();
                                uploadImg.setAttribute('data-id', response.image.uuid);

                                uploadImg.onload = function () {
                                    checkProfileImageState();
                                    setEditMode(editMode);
                                };

                                uploadImg.onerror = function () {
                                    this.src = defaultProfileImage;
                                    this.setAttribute('data-id', 'none');
                                    currentProfileImageUuid = '';
                                    mainImageUploadPending = false;
                                    checkProfileImageState();
                                };
                            }

                            if (modalUuid) modalUuid.value = response.image.uuid;
                        } else {
                            addAdditionalImageToDOM(response.image);
                            currentAdditionalCount++;
                            updateEmptySlots();
                        }

                        profileImageInput.value = '';
                        closeModal();
                    },
                    error: function (xhr, status, error) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            const message = response.error || 'Er is een fout opgetreden bij het uploaden';
                            uploadError.innerText = message;
                        } catch (e) {
                            uploadError.innerText = 'Er is een fout opgetreden bij het uploaden';
                        }
                    }
                });
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function createEmptySlot() {
                const slot = document.createElement('div');
                slot.className = 'empty-slot';
                slot.id = 'empty-slot-new-' + Date.now();
                slot.setAttribute('role', 'button');
                slot.setAttribute('tabindex', '0');
                slot.setAttribute('aria-label', 'Extra foto toevoegen');
                slot.addEventListener('click', function () { openAdditionalImageUpload(); });
                slot.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openAdditionalImageUpload();
                    }
                });

                const addSpan = document.createElement('span');
                addSpan.className = 'add';
                addSpan.setAttribute('aria-hidden', 'true');
                addSpan.textContent = '+';
                slot.appendChild(addSpan);

                return slot;
            }

            function addAdditionalImageToDOM(image) {
                const emptySlots = document.querySelectorAll('.empty-slot');
                if (emptySlots.length > 0) {
                    const firstEmptySlot = emptySlots[0];

                    const wrapper = document.createElement('div');
                    wrapper.className = 'profile-image-item';
                    wrapper.id = 'item-' + image.uuid;

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'close';
                    btn.setAttribute('aria-label', 'Verwijder afbeelding');
                    btn.textContent = '×';
                    btn.addEventListener('click', function () {
                        removeAdditionalImage(image.uuid, this);
                    });

                    const img = document.createElement('img');
                    img.src = '/' + image.path + '/' + image.name;
                    img.alt = image.description;

                    wrapper.appendChild(btn);
                    wrapper.appendChild(img);
                    firstEmptySlot.replaceWith(wrapper);
                }
            }

        </script>
        @if ($errors->has('profileImage'))
            <script>
                modal.style.display = 'block';
            </script>
        @endif
    @endsection
