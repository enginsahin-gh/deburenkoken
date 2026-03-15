<?php $googleUrl = "https://maps.google.com/maps/api/js?key="  . config('services.google_cloud.api_key') . "&libraries=places" ?>
    <script src="<?php echo $googleUrl; ?>" type="text/javascript"></script>
    <script>
        let stad = false;
        let searchButton = document.getElementById('zoeken');
        const store = document.getElementById('opslaan');
        const client = document.getElementById('client').value;
        const city = document.getElementById('city');
        const geocoder = new google.maps.Geocoder();
        const street = document.getElementById('street');
        const houseNumber = document.getElementById('number');
        const postalCode = document.getElementById('postal');
        const latitude = document.getElementById('latitude');
        const longitude = document.getElementById('longitude');
        const country = document.getElementById('country');
        const msg = document.getElementById('msg');
        const example = document.getElementById('example');
        const addition = document.getElementById('addition');
        const inputSearchButton = document.getElementById('searchButton');
        const searchInput = document.getElementById('autocomplete');
        let locationAcces = false;
        let autocompleteValue = false;
        let searchInputFocus = true;

        let createSearchTextElement = true;

        if (client === 'true') {
            searchButton = store;
        }

        $(document).ready(function(){

            initialize();
            searchInputDishesToggle();

        });


        document.getElementById('autocomplete').addEventListener('keydown', function (e) {

            if (e.key === 'Enter') {
                e.preventDefault();
                // Voer hier de logica uit die moet gebeuren wanneer Enter wordt ingedrukt.
                // Bijvoorbeeld, het ophalen van de adresgegevens zonder de lijst te sluiten.
            } 
            // if(createSearchTextElement === true){
            //     createSearchTextElement =  addSugestionText();
              
            // } 
                      
        });

        const cookSearch = document.getElementById('searchCook');
        if (cookSearch !== null) {
            searchButton.addEventListener('click', function (e) {
                e.preventDefault();

                if (document.getElementById('autocomplete').value.length === 0) {
                    cookSearch.submit();
                } else {
                    document.getElementById('autocomplete').value = null;
                    cookSearch.submit();
                }
            });
        }

        function searchInputDishesToggle(){
            if(inputSearchButton !== null) {
                document.addEventListener('click', function (event) {

                    const isClickInsideInput = searchInput.contains(event.target);
                    const isClickInsideButton = inputSearchButton.contains(event.target);

                    if (isClickInsideInput) {
                        inputSearchButton.classList.add('fa-times');
                        inputSearchButton.classList.remove('fa-magnifying-glass');
                    } else if (!isClickInsideButton) {
                        inputSearchButton.classList.remove('fa-times');
                        inputSearchButton.classList.add('fa-magnifying-glass');
                    }
                });

                    inputSearchButton.addEventListener('click', function () {
                        if (inputSearchButton.classList.contains('fa-times')) {
                            searchInput.value = '';
                            searchInput.focus();
                        } else {
                            if (searchInput.value == autocompleteValue && locationAcces === true) {
                               search();
                            } else{
                            searchInput.focus();
                            inputSearchButton.classList.add('fa-times');
                            inputSearchButton.classList.remove('fa-magnifying-glass');

                            }
                        }
                    });

                    searchButton.addEventListener('click', function (e) {
                    // e.preventDefault();
                    // searchInput.focus();
                    // inputSearchButton.classList.add('fa-times');
                    // inputSearchButton.classList.remove('fa-magnifying-glass');
                });
                
            }
        }



        document.getElementById('autocomplete').addEventListener('keypress', function (e) {

            if (e.key === 'Enter') {
                e.preventDefault();
                if (stad !== undefined) {
                    searchButton.disabled = false;
                    // searchButton.click();
                }
            }
        });
        function disableEnterKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                // Voer hier de gewenste acties uit op "Enter" toetsindruk

                this.blur();  // Verwijder de focus tijdelijk van de zoekinput
                if(searchInputFocus){
                    setTimeout(() => {
                        this.style.transition = 'none';  // Schakel overgang uit na animatie
                        this.focus(); // Zet de focus onmiddellijk terug naar de zoekinput
                    }, 0); // 200 milliseconden, pas aan indien nodig
                }
            }
        }

        document.getElementById('autocomplete').addEventListener('keydown', disableEnterKey);

        function search() {
                searchButton.disabled = false;
                searchButton.click();
                document.getElementById('autocomplete').blur();
        }

        function codeLatLng(lat, lng){
            let latlng = new google.maps.LatLng(lat, lng);

            geocoder.geocode({'latLng': latlng}, function(results, status){
                if (status === google.maps.GeocoderStatus.OK){
                    $('#autocomplete').val(results[0].address_components[2].long_name);

                    if (street !== null) {
                        // street.value = results[0].address_components[1].long_name;
                        // houseNumber.value = results[0].address_components[0].long_name;
                        // postalCode.value = results[0].address_components[6].long_name;
                        // country.value = results[0].address_components[5].short_name;
                        // city.value = results[0].address_components[2].long_name;
                        // console.log(results[0].address_components)
                        searchButton.disabled = false;
                    }

                    if (results[1]) {
                        for (let i=0; i<results[0].address_components.length; i++) {
                            for (let b=0;b<results[0].address_components[i].types.length;b++) {
                                if (results[0].address_components[i].types[b] === "administrative_area_level_1") {
                                    $('#autocomplete').val(results[0].formatted_address);
                                    autocompleteValue = results[0].formatted_address;
                                    latitude.value = results[0].geometry.location.lat();
                                    longitude.value = results[0].geometry.location.lng();
                                
                                    console.log(results);
                                    break;
                                }
                            }
                        }
                    }
                }
            });
        }

        function initialize(){
            let lat; 
            let lng;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
            }

            function successFunction(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude;
                // $('#latitude').val(lat);
                // $('#longitude').val(lng);
                locationAcces = true;
                codeLatLng(lat, lng);
            }

            function errorFunction(content) {
                if (content.message !== 'User denied Geolocation') {
                    locationAcces = false;
                    console.log('Geocoder failed');
                }
            }

            let input = document.getElementById('autocomplete');
            let autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.setComponentRestrictions(
                {'country': ['nl']});
            autocomplete.addListener('place_changed', function() {
                
                $(document).keypress(function(e) {
                    if (e.key === 'Enter') {
                        let firstResult = $(".pac-container .pac-item:first").text();
                        let geocoder = new google.maps.Geocoder();

                        geocoder.geocode({"address":firstResult }, function(results, status) {
                            if (status === google.maps.GeocoderStatus.OK) {
                                latitude.value = results[0].geometry.location.lat();
                                longitude.value = results[0].geometry.location.lng();
                                city.value = results[0].formatted_address;

                                if (client === 'true') {
                                    const address = results[0].address_components;
                                    houseNumber.value = address[0].long_name;
                                    street.value = address[1].long_name;
                                    postalCode.value = address[6].long_name;
                                    country.value = address[5].short_name;

                                    msg.style.fontSize = "13px";
                                    example.style.display = "none";
                                    store.disabled = false;
                                }
                                console.log('ss');
                                stad = true;
                                // search();
                            }
                        });
                    } else {
                        $(".pac-container").css("visibility","visible");
                    }
                });

                let change = autocomplete.getPlace();

                if (client === 'true' && stad === false) {
                    if (
                        change.address_components.length > 6 &&
                        change.address_components[0].long_name !== stad
                    ) {
                        const address = change.address_components;

                        for (let i = 0; i < address.length; i++) {
                            // console.log('starttest')
                            if (address[i].types[0] === 'street_number') {
                                var streetNumber = address[i].long_name;

                                // hier word de huisnummer gesplitst in huisnummer en toevoeging
                                
                                var streetNumberParts = streetNumber.split(/(\d+)/);

                                if (streetNumberParts.length === 3 && streetNumberParts[2] !== "") {
                                    houseNumber.value = streetNumberParts[1];
                                    addition.value = streetNumberParts[2];
                                } else {
                                    houseNumber.value = streetNumber;
                                    addition.value = null;
                                }
                            }


                            if (address[i].types[0] === 'route') {
                                street.value = address[i].long_name;
                            }
                            if (address[i].types[0] === 'locality') {
                                city.value = address[i].long_name;
                            }

                            if (address[i].types[0] === 'country') {
                                country.value = address[i].short_name;
                            }

                            if (address[i].types[0] === 'postal_code') {
                                postalCode.value = address[i].long_name;
                            }

                        }

                        $('#latitude').val(change.geometry.location.lat());
                        $('#longitude').val(change.geometry.location.lng());

                        msg.style.fontSize = "13px";
                        example.style.display = "none";
                        store.disabled = false;
                    } else {
                        houseNumber.value = null;
                        street.value = null;
                        city.value = null;
                        postalCode.value = null;
                        country.value = null;

                        latitude.value = null;
                        longitude.value = null;
                        store.disabled = true;

                        if (msg.style.fontSize === "20px") {
                            msg.style.color = "red";
                        }

                        msg.style.fontSize = "20px";
                        example.style.display = "block";
                    }
                } else {
                    if (change.geometry) {
                        latitude.value = change.geometry['location'].lat();
                        longitude.value = change.geometry['location'].lng();

                        city.value = change.formatted_address;
                        stad = true;
                        document.getElementById('autocomplete').value = city.value;

                        searchInputFocus = false

                        document.getElementById('autocomplete').blur();

                        search();
                    }
                }
            });            

        }

       

        function addSugestionText(){
        var pacContainer = document.querySelector('.pac-container');

        if (pacContainer) {
            var customText = document.createElement('div');
            customText.textContent = 'Suggesties';

            // Voeg de aangepaste tekst in vóór het huidige kind van .pac-container
            pacContainer.insertBefore(customText, pacContainer.firstChild);

            // Stijl voor de aangepaste tekst
            customText.classList.add('pac-head', 'pac-item-query');

            // Stel de z-index van het zoekvak in zodat het boven de aangepaste tekst wordt weergegeven
            return false;
            } 
        }


        
    </script>
