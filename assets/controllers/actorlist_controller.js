import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        function updateActorDropdown(data) {
            const actorInput = document.querySelector('.actor-input');
            const label = actorInput.nextSibling
            // Suppression de toutes les options existantes
            if (document.querySelector('.actor-dropdown')) {
                document.querySelector('.actor-dropdown').remove();
            }

            //si data est vide return
            if (data.length === 0) {
                return;
            }
            // while (label.nextSibling) {
            //     label.nextSibling.remove();
            // }
            // Création d'une liste déroulante avec les résultats
            const dropdown = document.createElement('select');
            dropdown.className = "form-select actor-dropdown";
            dropdown.multiple = true;
            dropdown.ariaLabel = "multiple select example";
            dropdown.style.minHeight = "100px";
            dropdown.style.paddingTop = "0.5rem";
            dropdown.style.marginTop = "0.5rem";

            const defaultOption = document.createElement('option');
            defaultOption.textContent = "--Please choose an option--";
            defaultOption.disable = true;
            dropdown.appendChild(defaultOption);

            data.forEach(function(result) {
                const option = document.createElement('option');
                option.textContent = result.name;
                option.setAttribute('data-actor-id', result.id);
                option.setAttribute('value', result.id);

                option.addEventListener('click', function() {
                    console.log('Actor selected:', result);
                    handleChoice(result);
                });
                dropdown.appendChild(option);
            });

            // Ajoutez la liste déroulante sous l'input
            label.parentNode.insertBefore(dropdown, label.nextSibling);
        }

        function handleChoice(actor) {

            const actorList = document.querySelector('.selected-actors-list');
            // Vérification de l'existance d'un bouton avec le même data-actor-id
            const existingButton = actorList.querySelector('button[data-actor-id="' + actor.id + '"]');

            if (existingButton) {
                console.log('Actor already selected:', actor);
                return;
            }

            const hiddenInput = addHiddenInput(actor);
            addActorButton(actor, hiddenInput);
        }

        function addActorButton(actor, hiddenInput) {

            const button = document.createElement('button');
            button.setAttribute('data-actor-id', actor.id);
            button.className = 'btn btn-primary btn-actor';

            const text = document.createElement("span");
            text.textContent = actor.name;

            const cross = document.createElement("i");
            cross.className = 'bi bi-x-square';

            button.appendChild(text);
            button.appendChild(cross);

            // Ajoutez un gestionnaire pour supprimer l'input hidden associé
            cross.addEventListener('click', function() {
                // const actorId = button.getAttribute('data-actor-id');
                // const hiddenInput = document.querySelector('input[name="selected_actors[]"][value="' + actorId + '"]');
                // console.log(hiddenInput);
                // if (hiddenInput) {
                //     hiddenInput.remove();
                //     console.log('Input removed:', actor);
                // }
                button.remove();
                console.log('Actor removed:', actor);
            });

            const actorList = document.querySelector('.selected-actors-list');
            actorList.appendChild(button);
        }

        // Fonction pour ajouter un champ caché au formulaire
        function addHiddenInput(actor) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selected_actors[]';
            hiddenInput.value = actor.id;
            document.querySelector('main > form').appendChild(hiddenInput);
            return hiddenInput;
        }


        document.addEventListener('DOMContentLoaded', function() {
            const actorInput = document.querySelector('.actor-input');

            actorInput.addEventListener('input', function() {
                const searchTerm = actorInput.value;

                // Effectuez la recherche d'acteurs et affichez les résultats
                fetch('/search/actor?term=' + searchTerm)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        updateActorDropdown(data);
                        // Mise à jour de l'affichage des résultats
                        // (Par exemple, créer une liste déroulante ou une liste sous l'input)
                    });
            });
        });

    }
}
