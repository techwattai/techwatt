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
        $(document).on("submit", "#psFrm,#psFrm1,#tm-form", async function(e) {
                e.preventDefault();
                const form = e.target;
                const btn = form.querySelector("button[type='submit']");
                const oBtnText = btn.textContent; btn.disabled = true; btn.textContent = "Submitting...";
                const formID = form.getAttribute("id");

                const formData = new FormData(form);
                const url = form.getAttribute("action");
                try {
                    const res = await fetch(url, {method: "POST", body: formData, credentials: 'same-origin', cache: "no-cache", headers: {"X-WP-Nonce": TWREST.nonce } });

                    let result;
                    if (res.ok) {  result = await res.json(); } else {
                        result = { success: false, message: `Request failed with status ${res.status} ${res.statusText}`};
                    }
                    
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
        ////////////////
        $(document).on("submit", "#psRegFrm", async function(e) {
                e.preventDefault();
                const form = e.target;
                const btn = form.querySelector("button[type='submit']");
                const oBtnText = btn.textContent; btn.disabled = true; btn.textContent = "Submitting...";
                const formID = form.getAttribute("id");

                const formData = new FormData(form);
                const url = form.getAttribute("action");
                try {
                    const res = await fetch(url, {method: "POST", body: formData, credentials: 'same-origin', cache: "no-cache",headers: {"X-WP-Nonce": TWREST.nonce } });
                    
                    const rawText = await res.text();
                    let result;

                    try {
                        result = JSON.parse(rawText);
                    } catch (e) {
                        result = { success: false, message: `Request failed with status ${rawText}`};
                    }

                    if (result.success) {
                        if(formID === 'psRegFrm'){ 
                            form.reset(); $("#psRegFrm").fadeOut(); 
                            
                            if(!result.paidnow){
                                $("#cmsgbox").fadeIn(); 
                            }else{
                                $("#pmsgbox").fadeIn(); 
                                $(".pbtnpay").data('uid', result.data.uid);
                                $(".pbtnpay").data('childid', result.data.childid);
                                $(".pbtnpay").data('amount', result.data.cost);
                                $(".pbtnpay").data('name', formData.get('parentname'));
                                $(".pbtnpay").data('email', formData.get('email'));
                                $("#pbtncost").html(` (£${result.data.cost})`);
                            }
                        }else{
                            showMessage(`${result.message}`,'success');
                        }
                        
                        if(formID === 'psFrm1' || formID === 'tm-form'){ form.reset(); }
                        if(formID === 'tm-form'){ 
                            window.reload();
                            //$("#tmcontainer").slideToggle();
                         }                         
                        history.replaceState(null, "", location.href);
                    } else {
                        showMessage(`${result.message}`,'error');
                    }
                } catch (err) {
                    showMessage(`${err.message || err}`,'error');
                } finally {
                    btn.disabled = false;
                    btn.textContent = oBtnText;
                }
        });
        ///////////////////////////
        $(document).on("change","#tw_package",function(){
            let dis = $(this).val();
            let inCourse = null;

            if(dis == 'bronze'){
                $("#packageinfo").show(100).html('Our <b>Bronze Package</b> is perfect for self-starters. It does not include a Robotics Kit, and students are required to purchase their own kit separately. Classes are conducted in larger groups of up to 30 students, offering a community-based learning experience at an affordable rate.');
                inCourse = ["robotics"];
            }else if(dis == 'silver'){
                $("#packageinfo").show(100).html('The <b>Silver Package</b> includes a Robotics Kit and provides interactive group lessons with up to 20 students per class. This package is ideal for learners who enjoy collaboration and teamwork while still receiving quality instruction.');
                inCourse = ["robotics"];
            }else if(dis == 'gold'){
                $("#packageinfo").show(100).html('Our <b>Gold Package</b> offers a complete Robotics experience. Students receive a Robotics Kit and enjoy personalized one-on-one sessions twice a week. Each learner also gets access to all previous class recordings, ensuring continuous learning and revision at their own pace.');
                inCourse = ["robotics"];
            }else{
                $("#packageinfo").html("").hide(10);
                inCourse = null;
            }
            
            $("#tw_course option").each(function() {
                if(inCourse){
                    //if (!$(this).val().includes(inCourse)) {
                    if (!inCourse.includes($(this).val())) {
                        if($(this).val() !== ''){ $(this).prop("disabled", true); }
                        else{ $(this).prop("selected", true); }
                    }else{
                        $(this).prop("disabled", false);
                    }
                }else{
                    $(this).prop("disabled", false);
                }                
            });
        });

        ////////////// Cancel Course //////////////////
        $(document).on('click', '#btn-cancel-course', function () {
            var btn = $(this); //NB: <a> link not button....
            
            if(!confirm("Are you sure you want to cancel this course booking? Deleting this course cannot be undone.")) { return; }

            const oBtnText = btn.html(); 
            btn.on("click.disabled", function(e) { e.preventDefault(); });  
            btn.html("Deleting...");

            var childid = btn.data('childid');
            var uid = btn.data('uid');
            var apiCall = btn.data('apicall');
            var restUrl = TWREST.root + apiCall;

            $.ajax({ url: restUrl, method: 'POST',
                beforeSend: function (xhr) { xhr.setRequestHeader('X-WP-Nonce', TWREST.nonce); },
                data: { childid: childid, uid: uid },
                success: function (response) {
                    if (response.success) {
                        alert(`Course #${childid} cancelled successfully.`);
                        location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function (err) {
                    console.log(err);
                },
                complete: function () { 
                    btn.html(oBtnText); btn.off("click.disabled");
                }
            });
        });

        //////////////// STRIPE PAYMENT //////////////////

        $(document).on('click', '#stripe-pay-btn', function () {
            var btn = $(this); //NB: <a> link not button....
            
            const oBtnText = btn.html(); 
            btn.on("click.disabled", function(e) { e.preventDefault(); });
            btn.html("Processing...");

            var amount = btn.data('amount');
            amount = parseFloat(amount).toFixed(2);
            var currency = btn.data('currency');
            var childid = btn.data('childid');
            var uid = btn.data('uid');
            var name = btn.data('name');
            var email = btn.data('email');

            var apiCall = btn.data('apicall');
            var restUrl = TWREST.root + apiCall;

            //const res = await fetch(restUrl, {method: "POST", body: formData, credentials: 'same-origin', cache: "no-cache" });

            $.ajax({ url: restUrl, method: 'POST',
                beforeSend: function (xhr) { xhr.setRequestHeader('X-WP-Nonce', TWREST.nonce); },
                data: { amount: amount, currency: currency, childid: childid, uid: uid, name: name, email: email },
                success: function (response) {
                    if (response.success) {
                        var stripe = Stripe(TWREST.stripe_pk);
                        stripe.redirectToCheckout({ sessionId: response.data.id });
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function (err) {
                    console.log(err);
                },
                complete: function () { 
                    btn.html(oBtnText); btn.off("click.disabled");
                }
            });
        });

});
////////// USER PORTAL //////////////////
const msg = document.getElementById("msgbox");

/*document.querySelector("#psFrm").addEventListener("submit", async function(e) {
  e.preventDefault();
});*/
