jQuery(document).ready(function($) {

        const icons = document.querySelectorAll('.tw-info-icon');
        icons.forEach(icon => {
                icon.addEventListener('click', function(e){
                    let existing = document.querySelector('.tw-info-popup');
                    if(existing) existing.remove();
                    const box = document.createElement('div');
                    box.className = 'tw-info-popup';
                    box.innerHTML = this.dataset.full;
                    Object.assign(box.style, {
                        position: 'absolute',
                        background: '#fff',
                        color: '#333',
                        border: '1px solid #ccc',
                        padding: '8px 10px',
                        fontSize: '13px',
                        maxWidth: '250px',
                        borderRadius: '6px',
                        boxShadow: '0 2px 6px rgba(0,0,0,0.2)',
                        zIndex: 9999,
                        top: (e.pageY + 5) + 'px',
                        left: (e.pageX + 5) + 'px'
                    });
                    document.body.appendChild(box);
                    const removePopup = () => { box.remove(); document.removeEventListener('click', removePopup); };
                    setTimeout(() => document.addEventListener('click', removePopup), 0);
                });
        });

    ///////////////////////////
        setTimeout(function() {
            var usernameInput = document.querySelector('input[name="username"]');
            var passwordInput = document.querySelector('input[type="password"]');            
            if (usernameInput) { usernameInput.value = ''; }
            if (passwordInput) { passwordInput.value = ''; }
        }, 500); // check every 500ms
        ////////////////////////
        function showMessage(message, type = "success") {
            const $msgBox = $("#msgbox");
            const colors = {
                success: { bg: "#d4edda", text: "#155724", icon: "✅" },
                error: { bg: "#f8d7da", text: "#721c24", icon: "❌" },
                warning: { bg: "#fff3cd", text: "#856404", icon: "⚠️" },
                info: { bg: "#cce5ff", text: "#004085", icon: "ℹ️" }
            };
            const style = colors[type] || colors.success;
            $msgBox.html(`${style.icon} ${message}`).css({"background-color": style.bg,"color": style.text,"display": "none"}).stop(true, true).fadeIn(200).delay(10000).fadeOut(600);
        }
        ////////////////////////////////
        $("#cbtncancel").on("click", function(){ $("#cmsgbox").fadeOut(); $("#psRegFrm").fadeIn(); });
        //////////////////////////////////////////
        $(document).on("submit", "#psRegFrm,#psFrm,#psFrm1,#tm-form", async function(e) {
                e.preventDefault();
                const form = e.target;
                const btn = form.querySelector("button[type='submit']");
                const oBtnText = btn.textContent; btn.disabled = true; btn.textContent = "Submitting...";
                const formID = form.getAttribute("id");

                const formData = new FormData(form);
                const url = form.getAttribute("action");

                try {
                    const res = await fetch(url, {method: "POST", body: formData });
                    const result = await res.json();
                    //console.log(JSON.stringify(result));
                    if (result.success) {
                        if(formID === 'psRegFrm'){ 
                            form.reset(); $("#psRegFrm").fadeOut(); $("#cmsgbox").fadeIn(); 
                        }else{
                            showMessage(`${result.data.message}`,'success');
                        }
                        
                        if(formID === 'psFrm1' || formID === 'tm-form'){ form.reset(); }
                        if(formID === 'tm-form'){ 
                            window.reload();
                            //$("#tmcontainer").slideToggle();
                         }                         
                        history.replaceState(null, "", location.href);
                    } else {
                        showMessage(`${result.data.message}`,'error');
                    }
                } catch (err) {
                    showMessage(`${err.message || err}`,'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = oBtnText;
                }
        });

});
////////// USER PORTAL //////////////////
const msg = document.getElementById("msgbox");

/*document.querySelector("#psFrm").addEventListener("submit", async function(e) {
  e.preventDefault();
});*/
