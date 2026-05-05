
document.addEventListener("DOMContentLoaded", function()
{

    document.getElementById("photo_profil").addEventListener("change", function() {
        const file = this.files[0];

        if(file){
            const reader = new FileReader();

            reader.onload = function(e){
                document.getElementById("avatarPreview").innerHTML =
                    '<img src="' + e.target.result + '" alt="Photo">';
            };

            reader.readAsDataURL(file);
        }
    });

 
    const cards = document.querySelectorAll(".doc-card");

    cards.forEach(function(card)
    {

        const input = card.querySelector(".upload-input");
        const btn = card.querySelector("label.doc-card__btn");
        const status = card.querySelector(".doc-status");
        const actions = card.querySelector(".doc-actions");

        let currentFileURL = "";

        input.addEventListener("change", function(){
            const file = this.files[0];

            if(file)
            {
                currentFileURL = URL.createObjectURL(file);

                btn.textContent = "Modifier";

                actions.style.display = "flex";

                actions.innerHTML = `
                    <span class="doc-card__btn view-btn">Voir</span>
                    <span class="doc-card__delete  delete-btn">Supprimer</span>
                `;

                actions.querySelector(".view-btn").addEventListener("click", function(){
                    window.open(currentFileURL);
                });

                actions.querySelector(".delete-btn").addEventListener("click", function(){
                    input.value = "";
                    btn.textContent = "Ajouter";
                    status.style.display = "inline-block";
                    actions.innerHTML = "";
                });
            }
        });

    });
    /* PHOTO DE PROFIL */

    const inputPhoto = document.getElementById("photo_profil");
    const avatarPreview = document.getElementById("avatarPreview");
    const photoLabel = document.getElementById("photoLabel");
    const photoActions = document.getElementById("photoActions");
    const deletePhoto = document.getElementById("deletePhoto");
    
    let currentPhoto = null;
    
    /* quand on choisit une photo */
    inputPhoto.addEventListener("change", function () 
    {
        const file = this.files[0];
    
        if(file)
        {
            const reader = new FileReader();
    
            reader.onload = function(e)
            {
                currentPhoto = e.target.result;
    
                avatarPreview.innerHTML = "";
                avatarPreview.style.backgroundImage = `url(${currentPhoto})`;
                avatarPreview.style.backgroundSize = "cover";
                avatarPreview.style.backgroundPosition = "center";
    
                photoLabel.style.display = "none";
                photoActions.style.display = "flex";
            }
    
            reader.readAsDataURL(file);
        }
    });
    
    /* supprimer */
    deletePhoto.addEventListener("click", function () 
    {
       window.location.href = "photo_actions.php?type=photo_profil";
    });
    
    /* cliquer sur photo = voir */
    avatarPreview.addEventListener("click", function () 
    {
        if(currentPhoto)
        {
            window.open(currentPhoto, "_blank");
        }
    });

});

