<section class="about-concept-section">
    <div class="container">

        <div class="row justify-content-center text-center mb-5 pb-4">
            <div class="col-lg-10">
                <span class="mini-tag text-orange">Η ΤΑΥΤΟΤΗΤΑ ΜΑΣ</span>
                <h1 class="concept-title mt-3">
                    Δύο λέξεις. <span class="serif-italic text-muted">Ένας σκοπός.</span>
                </h1>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">

            <div class="col-lg-6">
                <div class="concept-card card-latin">
                    <div class="blob-organic"></div>

                    <div class="content position-relative z-2">
                        <span class="lang-tag">LATIN</span>
                        <h2 class="word-title">Alma</h2>
                        <h3 class="word-sub">/ˈæl.mə/ • ουσιαστικό</h3>
                        <div class="divider-line"></div>
                        <p class="word-desc">
                            “Alma” στα λατινικά σημαίνει ψυχή, πνοή ζωής, αυτό που θρέφει και δίνει νόημα.
                            <br><br>
                            Είναι η εσωτερική δύναμη που κινεί τον άνθρωπο, η ουσία του εαυτού του, εκεί που γεννιούνται τα συναισθήματα, οι σκέψεις και τα όνειρα.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="concept-card card-greek">
                    <div class="beam-energy"></div>

                    <div class="content position-relative z-2">
                        <span class="lang-tag">GREEK</span>
                        <h2 class="word-title">Άλμα</h2>
                        <h3 class="word-sub">/ˈal.ma/ • ουσιαστικό</h3>
                        <div class="divider-line"></div>
                        <p class="word-desc">
                            “Άλμα» στα Ελληνικά συμβολίζει την κίνηση προς τα εμπρός, την αλλαγή, το θάρρος να αφήσουμε πίσω ό,τι μας κρατά και να προχωρήσουμε σε κάτι νέο.
                            <br><br>
                            Είναι το πέρασμα από το «εκεί που είμαι» στο «εκεί που μπορώ να φτάσω».
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="row justify-content-center mt-5 pt-5">
            <div class="col-lg-8 text-center">
                <div class="synthesis-box">

                    <div class="fusion-scene mb-4">
                        <div class="spin-wrapper">
                            <div class="color-trail"></div>

                            <div class="element-orb orb-soul"></div>
                            <div class="element-orb orb-leap"></div>
                        </div>

                        <div class="unified-core"></div>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <p class="synthesis-text mt-4">
                                Στο κέντρο μας το <strong>“alma”</strong> ενώνει αυτές τις δύο έννοιες: Τη φροντίδα της ψυχής και το άλμα προς την εξέλιξη.
                                Εδώ, κάθε άνθρωπος έχει το χώρο και το χρόνο να κατανοήσει τον εαυτό του και να κάνει το δικό του προσωπικό άλμα προς την αποδοχή, την αλλαγή, την ισορροπια και την προσωπική ανάπτυξη.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-5 flex-wrap">
                        <a href="/contact" class="btn-alma-outline">Επικοινωνία</a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    </div>
</section>


<style>
    /* ========================
   CONCEPT ANIMATIONS (REVISED)
   ======================== */

    .about-concept-section {
        padding: 160px 0;
        background-color: #fff;
        overflow: hidden;
    }

    .concept-card {
        padding: 80px 60px;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .concept-card:hover {
        transform: translateY(-10px);
    }

    /* --- LATIN CARD (WARM & ORGANIC) --- */
    .card-latin {
        background-color: #f9f7f2;
        color: var(--alma-text);
    }

    /* Ένα μεγάλο "υγρό" σχήμα που κινείται αργά στο background */
    .blob-organic {
        position: absolute;
        top: -30%;
        right: -30%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, #fae3d9 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        filter: blur(50px);
        opacity: 0.6;
        animation: morphBackground 15s infinite alternate ease-in-out;
    }

    @keyframes morphBackground {
        0% {
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            transform: rotate(0deg);
        }

        100% {
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            transform: rotate(20deg);
        }
    }

    /* --- GREEK CARD (DYNAMIC & UPWARD) --- */
    .card-greek {
        background-color: #f4f4f4;
        color: var(--alma-text);
    }

    /* Μια δέσμη φωτός που ανεβαίνει */
    .beam-energy {
        position: absolute;
        bottom: -20%;
        right: -10%;
        width: 400px;
        height: 600px;
        background: linear-gradient(15deg, rgba(200, 200, 200, 0.2), transparent);
        filter: blur(40px);
        transform: skewY(-20deg);
        animation: pulseBeam 8s infinite alternate ease-in-out;
    }

    @keyframes pulseBeam {
        0% {
            opacity: 0.3;
            transform: skewY(-20deg) translateY(0);
        }

        100% {
            opacity: 0.6;
            transform: skewY(-20deg) translateY(-30px);
        }
    }


    /* ========================
   COMPACT VORTEX STYLES (SIDE-BY-SIDE)
   ======================== */
    .fusion-scene {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spin-wrapper {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    /* --- THE COLOR WHEEL TRAIL --- */
    .color-trail {
        position: absolute;
        top: 15%;
        left: 15%;
        /* Λίγο πιο μικρό για να μείνει στο κέντρο */
        width: 70%;
        height: 70%;
        border-radius: 50%;
        /* Gradient που ενώνει τα χρώματα */
        background: conic-gradient(from 90deg,
                var(--alma-orange),
                var(--alma-bg-button-main),
                var(--alma-orange));
        filter: blur(12px);
        opacity: 0;
        z-index: 0;
    }

    /* --- ORBS (PLANETS) --- */
    .element-orb {
        position: absolute;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        z-index: 2;
        /* Κεντράρισμα στον κάθετο άξονα */
        top: 50%;
        transform: translateY(-50%);
    }

    .orb-soul {
        /* LEFT SIDE */
        background: radial-gradient(circle at 30% 30%, #ffdcb8, var(--alma-orange));
        box-shadow: 0 0 10px rgba(233, 168, 113, 0.5);

        /* Θέση: Αριστερά αλλά κοντά στο κέντρο */
        left: 10px;
    }

    .orb-leap {
        /* RIGHT SIDE */
        background: radial-gradient(circle at 30% 30%, #e8f5f0, var(--alma-bg-button-main));
        box-shadow: 0 0 10px rgba(137, 194, 170, 0.5);

        /* Θέση: Δεξιά αλλά κοντά στο κέντρο */
        right: 10px;
    }

    /* --- THE CORE (RESULT) --- */
    .unified-core {
        position: absolute;
        width: 85px;
        height: 85px;
        border-radius: 50%;

        /* Gradient Result */
        background: radial-gradient(circle at 30% 30%, #fff 10%, var(--alma-orange) 50%, var(--alma-bg-button-main) 100%);

        transform: scale(0);
        z-index: 3;
        box-shadow: 0 0 50px rgba(233, 168, 113, 0.4);
    }

    /* --- TYPOGRAPHY & BUTTONS --- */
    .lang-tag {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 2px;
        color: #999;
        margin-bottom: 15px;
        display: block;
    }

    .word-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        margin-bottom: 5px;
        line-height: 1;
        position: relative;
        z-index: 2;
    }

    .word-sub {
        font-family: 'Manrope', sans-serif;
        font-size: 1rem;
        font-style: italic;
        color: #888;
        margin-bottom: 30px;
    }

    .divider-line {
        width: 50px;
        height: 2px;
        background-color: var(--alma-orange);
        margin-bottom: 30px;
    }

    .word-desc {
        font-size: 1.15rem;
        line-height: 1.8;
        position: relative;
        z-index: 2;
    }

    .synthesis-text {
        font-size: 1.25rem;
        color: #555;
        line-height: 1.8;
        margin-bottom: 20px;
    }

    /* Buttons */
    .btn-alma-solid {
        background-color: var(--alma-orange);
        color: #fff;
        padding: 12px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        border: 1px solid var(--alma-orange);
        transition: all 0.3s ease;
    }

    .btn-alma-solid:hover {
        background-color: #333;
        border-color: #333;
        transform: translateY(-2px);
    }

    .btn-alma-outline {
        background-color: transparent;
        color: var(--alma-text);
        padding: 12px 35px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        border: 1px solid var(--alma-text);
        transition: all 0.3s ease;
    }

    .btn-alma-outline:hover {
        background-color: var(--alma-text);
        color: #fff;
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .about-concept-section {
            padding: 140px 0;
        }

        .concept-card {
            padding: 40px 30px;
            margin-bottom: 20px;
        }

        .word-title {
            font-size: 2.8rem;
        }
    }
</style>


<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            gsap.registerPlugin(ScrollTrigger);

            const fusionTl = gsap.timeline({
                scrollTrigger: {
                    trigger: ".fusion-scene",
                    start: "top 75%",
                    toggleActions: "play none none none"
                }
            });

            // --- STEP 1: PREPARE TRAIL ---
            fusionTl.to(".color-trail", {
                opacity: 0.6,
                duration: 0.5
            });

            // --- STEP 2: SPIN & CONVERGE (Horizontal) ---
            // Περιστροφή όλου του συστήματος
            fusionTl.to(".spin-wrapper", {
                rotation: 360,
                duration: 1.5,
                ease: "power2.inOut"
            }, "<");

            // Η αριστερή μπάλα πάει προς το κέντρο
            fusionTl.to(".orb-soul", {
                left: "50%",
                x: "-50%", // Ακριβές κεντράρισμα (μείον το μισό της πλάτος)
                scale: 0.6,
                duration: 1.5,
                ease: "power2.inOut"
            }, "<");

            // Η δεξιά μπάλα πάει προς το κέντρο
            fusionTl.to(".orb-leap", {
                right: "50%",
                x: "50%", // Ακριβές κεντράρισμα (μείον το μισό της πλάτος, αλλά από δεξιά)
                scale: 0.6,
                duration: 1.5,
                ease: "power2.inOut"
            }, "<");

            // Το Trail μικραίνει και σβήνει
            fusionTl.to(".color-trail", {
                scale: 0.2,
                opacity: 0,
                duration: 1.2,
                delay: 0.3
            }, "<");


            // --- STEP 3: FUSION POP ---
            fusionTl.to([".orb-soul", ".orb-leap"], {
                opacity: 0,
                duration: 0.1
            });

            fusionTl.to(".unified-core", {
                scale: 1,
                duration: 0.5,
                ease: "back.out(1.5)"
            });

            // --- STEP 4: ALIVE ---
            fusionTl.to(".unified-core", {
                scale: 1.08,
                boxShadow: "0 0 60px rgba(137, 194, 170, 0.4)",
                duration: 2,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });

        });
    </script>
<?php
}
?>