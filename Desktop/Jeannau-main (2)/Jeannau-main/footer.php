<?php
/**
 * Footer
 */
?>

<?php 
$contact_status = isset($_GET['contact_status']) ? $_GET['contact_status'] : '';
?>

<style>
/* Fix contact form cutting off issues */
#contact-form {
    min-height: 100vh;
    overflow: visible;
    margin-bottom: 150px;
    padding: 10px;
}

.step-panel {
    min-height: 600px;
    overflow: visible;
}

.agency-form {
    min-height: 800px;
    padding: 30px;
}

/* Ensure form is fully visible */
#contact-form-collapse {
    min-height: 700px;
}

/* Fix any container overflow issues */
.container {
    overflow: visible;
}

</style>

<?php if ($contact_status): ?>
<div style="text-align: center; padding: 15px; margin: 20px; border-radius: 5px;">
    <?php if ($contact_status === 'ok'): ?>
    <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px;">
        <strong>Success!</strong> Your message has been sent successfully. We'll contact you soon.
    </div>
    <?php elseif ($contact_status === 'nonce_fail'): ?>
    <div style="background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; padding: 10px;">
        <strong>Error!</strong> Security check failed. Please try again.
    </div>
    <?php elseif ($contact_status === 'db_error'): ?>
    <div style="background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; padding: 10px;">
        <strong>Error!</strong> There was a problem saving your message. Please try again.
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<section class="container px-5 contact mb-3 pb-5" id="contact-form">
    <img src="https://www.jeanneau.com/images/jeanneau-white-small.svg" />

    <div class="agency-form">
        <a href="" id="form" class="anchor"></a>

        <div class="big-title">
            <h3>Request to be contacted by a Dealer</h3>
        </div>

        <div id="contact-form-collapse" aria-multiselectable="true" role="tablist">
            <ul class="links-collapse nav justify-content-center" style="gap:.75rem; list-style:none; padding-left:0;">
                <li class="nav-item">
                    <a class="nav-link active js-link-contact" href="#js-collapse-contact" role="button" aria-expanded="true" aria-controls="js-collapse-contact">
                        1. Contact information
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-link-agency" href="#js-collapse-agency" role="button" aria-expanded="false" aria-controls="js-collapse-agency">
                        2. Your home port
                    </a>
                </li>
            </ul>

            <form name="model_contact"
                  method="post"
                  action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"
                  id="model_contact">
                <?php wp_nonce_field('contact_simple_nonce', 'contact_simple_nonce'); ?>
                <input type="hidden" name="action" value="submit_contact_simple" />

                <!-- STEP 1 -->
                <div id="js-collapse-contact" class="step-panel" data-step="contact" style="display:block;">
                    <fieldset>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label required">Title *</label>
                                    <div id="model_contact_civility" class="floatlabel" title="Title *">
                                        <div class="radio">
                                            <label for="model_contact_civility_0" class="required">
                                                <input type="radio" id="model_contact_civility_0" name="model_contact[civility]" required value="mr" checked>
                                                MR
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label for="model_contact_civility_1" class="required">
                                                <input type="radio" id="model_contact_civility_1" name="model_contact[civility]" required value="ms">
                                                MS
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="postal-address">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label required" for="model_contact_first_name">First Name *</label>
                                        <input type="text" id="model_contact_first_name" name="model_contact[first_name]" required class="floatlabel form-control" title="First Name *">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label required" for="model_contact_last_name">Last Name *</label>
                                        <input type="text" id="model_contact_last_name" name="model_contact[last_name]" required class="floatlabel form-control" title="Last Name *">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label required" for="model_contact_email">Email *</label>
                                        <input type="email" id="model_contact_email" name="model_contact[email]" required placeholder="example@email.com" class="floatlabel form-control" title="Email *">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="model_contact_phone">Mobile</label>
                                        <input type="text" id="model_contact_phone" name="model_contact[phone]" class="floatlabel form-control" title="Mobile">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="model_contact_address_street">Address</label>
                                        <input type="text" id="model_contact_address_street" name="model_contact[address_street]" class="floatlabel form-control" title="Address">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="model_contact_address_code">Postal code</label>
                                        <input type="text" id="model_contact_address_code" name="model_contact[address_code]" class="floatlabel form-control" title="Postal code">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label" for="model_contact_address_locality">City</label>
                                        <input type="text" id="model_contact_address_locality" name="model_contact[address_locality]" class="floatlabel form-control" title="City">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label required" for="model_contact_address_country">Country</label>
                                        <div class="select-wrapper">
                                            <select id="model_contact_address_country" name="model_contact[address_country]" style="width:100%" title="Country" class="form-control">
                                                <option value="AL">Albania</option>
                                                <option value="FR" selected>France</option>
                                                <option value="US">United States</option>
                                                <option value="GB">United Kingdom</option>
                                                <!-- Add other countries as needed -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label required" for="model_contact_address_state">State/Province</label>
                                        <div class="select-wrapper">
                                            <select id="model_contact_address_state" name="model_contact[address_state]" class="floatlabel form-control" title="State/Province"></select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="model_contact_message">Message</label>
                                        <textarea id="model_contact_message" name="model_contact[message]" rows="10" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row nota-container">
                                <div class="col-sm-12">
                                    <div class="nota">* Required Fields</div>
                                </div>
                            </div>

                            <div class="submit">
                                <a class="btn btn-primary btn-agency" href="#js-collapse-agency" data-step-go="agency" role="button" aria-controls="js-collapse-agency">
                                    Continue
                                </a>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- STEP 2 -->
                <div id="js-collapse-agency" class="step-panel" data-step="agency" style="display:none;">
                    <fieldset>
                        <div class="row">
                            <div class="col-sm-4 col-md-offset-2">
                                <div class="form-group">
                                    <label for="model_contact_agency">Dealer</label>
                                    <select id="model_contact_agency" style="width:100%;" class="form-control">
                                        <option value="">Select a dealer...</option>
                                        <option value="1">Marina Blue Coast</option>
                                        <option value="2">Atlantic Boat Center</option>
                                        <option value="3">Mediterranean Yachts</option>
                                        <option value="4">Adriatic Marine</option>
                                        <option value="5">Barcelona Nautical</option>
                                        <option value="6">Miami Boat Sales</option>
                                        <option value="7">San Diego Marine</option>
                                        <option value="8">Thames Boating</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="model_contact_city">City/State/Country</label>
                                    <select id="model_contact_city" style="width:100%;" class="form-control">
                                        <option value="">Select a location...</option>
                                        <option value="nice-fr">Nice, France</option>
                                        <option value="larochelle-fr">La Rochelle, France</option>
                                        <option value="marseille-fr">Marseille, France</option>
                                        <option value="split-hr">Split, Croatia</option>
                                        <option value="barcelona-es">Barcelona, Spain</option>
                                        <option value="miami-us">Miami, USA</option>
                                        <option value="sandiego-us">San Diego, USA</option>
                                        <option value="london-uk">London, UK</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="agency-list-header" id="model_contact_agency_list_header">
                                    <div class="header">Results</div>
                                </div>
                                <div class="agency-list" id="model_contact_agency_list"></div>
                                <div class="agency-choose">
                                    <div class="control-label required">Dealer choose*</div>
                                    <p class="dealer-choose" id="model_contact_agency_name" data-default="None">None</p>
                                    <span id="model_contact_agency_logo"></span>
                                    <input type="hidden" id="model_contact_agency_id" name="model_contact[agency_id]">
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div id="model_contact_map"
                                     style="width: 100%; height: 500px;"
                                     data-i18n-choose="Choose this dealer"
                                     data-location="[-1.0318501,46.864698099999998]"
                                     data-zoom="6"
                                     data-mapbox-key="pk.eyJ1IjoiamVhbm5lYXVnaSIsImEiOiJjamg3bWN0MncwZDJjMzNzYXhmeDJ3NzRmIn0.ZHZwH5H5Mh-WO0SjVrRpRg"
                                     data-category="outboard">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label for="model_contact_receive_newsletter">
                                            <input type="checkbox" id="model_contact_receive_newsletter" name="model_contact[receive_newsletter]" value="1">
                                            I would like to receive the Jeanneau newsletter.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <br>

                    <fieldset>
                        <div class="form-error captcha"></div>
                        <div class="h-captcha" data-sitekey="0d99254e-570f-4a7e-a19c-d6282fec20e6"></div>
                    </fieldset>

                    <fieldset>
                        <div class="row">
                            <div class="col-sm-12">
                                <p class="unsetFontSize">
                                    We need to transfer the personal data required in this form to the dealer you have selected to process your request. This is to allow them to contact you. If you click on the "SEND" button, you are agreeing to the transfer of your personal data.
                                </p>
                            </div>
                        </div>
                    </fieldset>

                    <div class="submit">
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>

                    <div>
                        <p>JEANNEAU refers to SPBI, the data controller for all geographical zones, with the exception of the Americas zone for which Beneteau Group America Inc acts as data controller. Your personal data is processed in order to respond to your request, manage our relationship with you and, if you have so chosen, send you our communications (in this case, you may unsubscribe at any time by using the link contained in our mailings).</p>
                        <p>To exercise your rights: contact.rgpd@beneteau-group.com</p>
                        <p>To find out more about the protection of your personal data:
                            <a href="https://www.beneteau-group.com/en/privacy-and-cookies-policy/" target="_blank" rel="noopener">privacy policy</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

    <footer class="site-footer" role="contentinfo">
        <div id="newsletter" class="container-fluid">
    <div class="media-container">
        <div class="video-container">
            <video loop playsinline muted preload="none" poster="https://www.jeanneau.com/build/images/footer/newsletter_default.jpg">
                                    <source src="https://www.jeanneau.com/build/images/footer/newsletter_motorboat.mp4" type="video/mp4">
                            </video>
        </div>
    </div>
    <div class="form">
        <form action="/newsletter" method="post">
            <div class="header">
                <div class="head">COME CRUISE WITH US !</div>
                <small>Subscribe to our newsletter</small>
            </div>
            <div>
                <div class="col-xs-6 col-xs-offset-1 col-sm-6 col-sm-offset-1 col-md-4 col-md-offset-3 col-lg-3 col-lg-offset-4">
                                        <input id="newsletter_email" class="form-control" type="email"
                           placeholder="Enter your email address"
                           title="Enter your email address" required="required" name="email">
                </div>
                <div class="col-xs-4 col-md-2 col-lg-1">
                    <button class="btn btn-tertiary btn-block" type="submit">Sign up</button>
                    <a href="" class="close"><i class="icon icon-close"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>
        <a id="footer" tabindex="-1"></a>
                    <div class="container footer-main">
    <nav id="footer-main" class="row">
        <h2 class="sr-only">Footer navigation</h2>
                    <section class="col-sm-3">
                <h3>Jeanneau</h3>
                <ul>
                                            <li><a href="/services/rent">Rent</a></li>
                        <li><a href="/services/second-hand">Used boats</a></li>
                                        <li><a href="/articles#events">Boat Shows and Events</a></li>
                    <li><a href="/comparator">Model Comparison Tool</a></li>
                </ul>
            </section>
                                                <section class="col-sm-3">
                        <h3><a href="/boats/sailboat">Sailboats</a></h3>
                        <ul>
                                                            <li><a href="/boats/sailboat/2-sun-odyssey">Sun Odyssey</a></li>
                                                            <li><a href="/boats/sailboat/4-jeanneau-yachts">Jeanneau Yachts</a></li>
                                                            <li><a href="/boats/sailboat/1-sun-fast">Sun Fast</a></li>
                                                        <li><a href="/models-panorama?category=sailboat">Virtual tour</a></li>
                        </ul>
                    </section>
                                                                <section class="col-sm-3">
                        <h3><a href="/boats/powerboat">Motorboats</a></h3>
                        <ul>
                                                            <li><a href="/boats/powerboat/39-cap-camarat">Cap Camarat</a></li>
                                                            <li><a href="/boats/powerboat/44-db-yachts">DB Yachts</a></li>
                                                            <li><a href="/boats/powerboat/9-merry-fisher">Merry Fisher</a></li>
                                                            <li><a href="/boats/powerboat/43-merry-fisher-sport">Merry Fisher Sport</a></li>
                                                        <li><a href="/models-panorama?category=powerboat">Virtual tour</a></li>
                        </ul>
                    </section>
                                        <section class="col-sm-3">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="https://help.jeanneau.com/hc/en-gb" target="_blank">Customer Service</a></li>
                </ul>
            </section>
            </nav>
</div>

<div role="contentinfo" id="footer-links" >
    <div class="social-links" itemscope itemtype="http://schema.org/Organization">
        <link itemprop="url" href="https://www.jeanneau.com">
        <ul>
                
            <li>
            <a href="https://www.facebook.com/JeanneauOfficial/" itemprop="sameAs" target="_blank" title="Facebook (Opens in a new window)">
                <i class="icon icon-facebook circle-icon"></i>
                <span class="sr-only">Facebook</span>
            </a>
        </li>
            <li>
            <a href="https://www.instagram.com/jeanneau_official/" itemprop="sameAs" target="_blank" title="Instagram (Opens in a new window)">
                <i class="icon icon-instagram circle-icon"></i>
                <span class="sr-only">Instagram</span>
            </a>
        </li>
            <li>
            <a href="https://fr.linkedin.com/company/jeanneau" itemprop="sameAs" target="_blank" title="Linked in (Opens in a new window)">
                <i class="icon icon-linkedin circle-icon"></i>
                <span class="sr-only">Linked in</span>
            </a>
        </li>
            <li>
            <a href="https://www.youtube.com/channel/UCN6KV7C7nUigyjTLKdE4Ysg?sub_confirmation=1" itemprop="sameAs" target="_blank" title="Youtube (Opens in a new window)">
                <i class="icon icon-youtube circle-icon"></i>
                <span class="sr-only">Youtube</span>
            </a>
        </li>
            <li>
            <a href="https://twitter.com/etsjeanneau" itemprop="sameAs" target="_blank" title="X (Opens in a new window)">
                <i class="icon icon-x circle-icon"></i>
                <span class="sr-only">X</span>
            </a>
        </li>
    </ul>
    </div>
    <ul class="other-links">
        <li><a href="/sitemap">Site Map</a></li>
        <li><a href="/legal">Legal Notice</a></li>
        <li><a href="/privacy-policy">Personal Data</a></li>
        <li><a href="/jeanneau/partners">Our Partners</a></li>
        <li><a href="/websites">All Our Websites</a></li>
        <li><a href="/accessibility">Accessibility</a></li>
        <li><a href="/contact">Contact</a></li>
        <li><button id="ot-sdk-btn" class="ot-sdk-show-settings footer"> Cookie Settings</button></li>
        <li><a id="optout" data-trk="optout" href="#">Reject audience measurement cookies</a></li>
    </ul>
    <div class="clearfix"></div>
</div>


                        </footer>

    <div id="cookieconsent-i18n"
         data-message="By continuing your visit on this website, you agree to the use of cookies to enhance your browsing experience and to generate website traffic statistics."
         data-dismiss="I AGREE"></div>


    <script src="/build/runtime.32cc791b.js" defer></script><script src="/build/691.570663c4.js" defer></script><script src="/build/268.9a434bd2.js" defer></script><script src="/build/732.a73f4830.js" defer></script><script src="/build/app.bab1e4dd.js" defer></script>
</body>
</html>

<?php wp_footer(); ?>
</body>
</html>

<script>
(function() {
    function show(stepId) {
        document.querySelectorAll('.step-panel').forEach(p => p.style.display = 'none');
        var el = document.querySelector(stepId);
        if (el) el.style.display = 'block';

        // Update active tab classes
        document.querySelectorAll('.links-collapse .nav-link').forEach(a => a.classList.remove('active'));
        var tab = document.querySelector('.links-collapse .nav-link[href="' + stepId + '"]');
        if (tab) tab.classList.add('active');
    }

    // Click handlers on the tabs
    document.querySelectorAll('.links-collapse .nav-link').forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            show(this.getAttribute('href'));
        });
    });

    // "Continue" button in step 1
    var cont = document.querySelector('[data-step-go="agency"]');
    if (cont) {
        cont.addEventListener('click', function(e) {
            e.preventDefault();
            show('#js-collapse-agency');
        });
    }

    // Form validation before submission
    var form = document.getElementById('model_contact');
    if (form) {
        form.addEventListener('submit', function(e) {
            var requiredFields = form.querySelectorAll('[required]');
            var isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Show loading state
            var submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Sending...';
                submitBtn.disabled = true;
            }
        });
    }

    // Clear URL parameters after showing status message
    if (window.location.search.includes('contact_status')) {
        setTimeout(function() {
            if (history.replaceState) {
                history.replaceState(null, null, window.location.pathname);
            }
        }, 5000);
    }
})();
</script>
