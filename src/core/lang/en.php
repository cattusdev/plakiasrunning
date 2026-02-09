<?php

if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");


$GLOBAL_LANGUAGE_OBJ['en']['main_title'] = 'Website Title';

//Nav menu
$GLOBAL_LANGUAGE_OBJ['en']['nav_home'] = 'Home';
$GLOBAL_LANGUAGE_OBJ['en']['accept_cookies'] = 'Accept cookies?';


$GLOBAL_LANGUAGE_OBJ['en']['privacy_policy_text'] = '<div class="row wedo-title">
    <div class="col-sm p-4 py-1">
        <h3 class="d-inline">Privacy Policy</h3>
    </div>
</div>
<div class="row">
    <div class="col-sm wedo-description legal p-4">
        <h4 class="Subtitle">Introduction</h4>
        <p class="text-left">' .  $mainSettings->companySettings->companyName . ' ("we", "us", "our") respects your privacy and is committed to protecting your personal data. This Privacy Policy outlines how we collect, use, and share information about you when you use our website ' . str_replace('https://', '', $mainSettings->companySettings->companyUrl) . ' ("Service"). By using our Service, you agree to the collection and use of information in accordance with this policy.</p>

        <h4 class="Subtitle">What Personal Data We Collect and How We Collect It</h4>
        <p class="text-left"><strong>Website Forms:</strong> We collect personal information such as your name, email address, country of residence, company name, industry, and inquiry details when you fill out forms on our website. This information is used to respond to your inquiries.</p>
        <p class="text-left"><strong>Cookies and Web Beacons:</strong> We use cookies and web beacons to collect data on how you use our website. This includes information about the pages you visit, the duration of your visit, and your IP address. We also use Google Analytics to collect information about your interaction with our website.</p>
        <p class="text-left"><strong>Career Applications:</strong> If you submit your resume and other personal details through our Careers page, we use this information to consider you for employment and for future opportunities.</p>
        <p class="text-left"><strong>Communication Subscriptions:</strong> We collect your name, email address, and company information when you subscribe to our communications. We also track your preferences regarding newsletters, thought leadership content, and marketing materials.</p>
        <p class="text-left"><strong>Email Communications:</strong> We track when you receive, open, click links in, or share our emails. To unsubscribe from marketing communications, click the unsubscribe link in our emails.</p>
        <p class="text-left"><strong>Information from Other Sources:</strong> We may receive information about you from public sources, social media, directories, and registries to ensure the accuracy and completeness of your personal data.</p>

        <h4 class="Subtitle">Purpose and Lawful Basis for Processing Personal Data</h4>
        <p class="text-left"><strong>Consent:</strong> Where we require your consent, you will find opt-in mechanisms and the ability to withdraw your consent at any time.</p>
        <p class="text-left"><strong>Contract Performance:</strong> We process your data to perform our services for you or your company.</p>
        <p class="text-left"><strong>Legal Obligations:</strong> We process data to comply with legal or regulatory obligations, including anti-money laundering, fraud prevention, and compliance checks.</p>
        <p class="text-left"><strong>Legitimate Interests:</strong> We process data for our legitimate interests, such as market research, marketing, IT security, and fraud prevention, provided these interests are not overridden by your rights.</p>

        <h4 class="Subtitle">Security</h4>
        <p class="text-left">We implement industry-standard security measures to protect your personal data, including restricting access to necessary personnel and using physical, electronic, and procedural safeguards.</p>

        <h4 class="Subtitle">Cookies and IP Data</h4>
        <p class="text-left">We use cookies to enhance your user experience and collect IP addresses to diagnose technical problems, analyze trends, and administer our site. We also use Google Analytics to analyze site usage. You can manage cookie settings through your browser.</p>

        <h4 class="Subtitle">Third Parties</h4>
        <p class="text-left">We may share your data with third-party service providers for cloud storage, legal compliance, and business transactions. These providers only use your data for specified purposes and must protect it adequately.</p>

        <h4 class="Subtitle">Data Storage and Retention</h4>
        <p class="text-left">We retain your data for as long as necessary to fulfill the purposes outlined in this policy or to comply with legal requirements. We assess retention periods based on data sensitivity, potential risks, and legal obligations.</p>

        <h4 class="Subtitle">Automated Profiling</h4>
        <p class="text-left">We use automated profiling for email campaigns to evaluate your interest in our services and provide relevant content.</p>

        <h4 class="Subtitle">Data Subject Rights</h4>
        <p class="text-left">Under the GDPR, you have rights regarding your personal data, including access, rectification, erasure, restriction of processing, data portability, and the right to object to automated processing. To exercise these rights, contact us at <a href="' . $mainSettings->companySettings->contactEmail . '">' . $mainSettings->companySettings->contactEmail . '</a>.</p>

        <h4 class="Subtitle">Contact Us</h4>
        <p class="text-left">For questions or concerns about your data, contact us at <a href="mailto:' . $mainSettings->companySettings->contactEmail . '">' . $mainSettings->companySettings->contactEmail . '</a>.</p>

        <h4 class="Subtitle">Complaints</h4>
        <p class="text-left">You have the right to lodge a complaint with a supervisory authority if you believe your data protection rights have been violated.</p>

        <h4 class="Subtitle">Changes to Our Privacy Policy</h4>
        <p class="text-left">This policy was last updated on 07/20/2024. We will notify you of any changes where required. Please review this policy periodically for updates.</p>
    </div>
</div>';


//Cookies Policy
$GLOBAL_LANGUAGE_OBJ['en']['cookies_policy_text'] = '
<div class="row wedo-title">
            <div class="col-sm p-4 py-1">
                <h3 class="d-inline">Cookies Policy</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm wedo-description legal p-4">
                <h4 class="Subtitle">What Are Cookies?</h4>
                <p class="text-left">Cookies are small text files stored on your device by websites you visit. They are used to make websites work more efficiently and to provide information to site owners.</p>

                <h4 class="Subtitle">How We Use Cookies</h4>
                <p class="text-left"><strong>Session Cookies:</strong> These temporary cookies are deleted when you close your browser.</p>
                <p class="text-left"><strong>Persistent Cookies:</strong> These cookies remain on your device until they expire or you delete them. They are used to remember your preferences and enhance your experience.</p>
                <p class="text-left"><strong>Web Beacons and Pixels:</strong> We use these technologies in emails and on our website to monitor activity and improve our service.</p>

                <h4 class="Subtitle">Managing Cookies</h4>
                <p class="text-left">You can control the use of cookies through your browser settings. Disabling cookies may affect the functionality of our website.</p>

                <h4 class="Subtitle">Cookies We Use</h4>
                <p class="text-left"><strong>Essential Cookies:</strong> Necessary for website functionality.</p>
                <p class="text-left"><strong>Analytics Cookies:</strong> Used to collect information about how visitors use our site, including Google Analytics.</p>
                <p class="text-left"><strong>Marketing Cookies:</strong> Used to track visitors across websites to display relevant ads.</p>

                <h4 class="Subtitle">Consent Mechanism</h4>
                <p class="text-left">By continuing to use our website, you consent to our use of cookies. You can manage your cookie preferences at any time through your browser settings.</p>

                <h4 class="Subtitle">Google Analytics</h4>
                <p class="text-left">We use Google Analytics to collect information about how visitors use our site. Google Analytics collects information such as how often users visit our site, what pages they visit, and what other sites they used prior to coming to our site. We use this information to improve our Service. Google Analytics collects only the IP address assigned to you on the date you visit our site, rather than your name or other identifying information. We do not combine the information collected through Google Analytics with personally identifiable information. Although Google Analytics plants a persistent Cookie on your web browser to identify you as a unique user the next time you visit our site, the Cookie cannot be used by anyone but Google. Google\'s ability to use and share information collected by Google Analytics about your visits to our site is restricted by the Google Analytics Terms of Use and the Google Privacy Policy. You can prevent Google Analytics from recognizing you on return visits to this site by disabling cookies on your browser.</p>
            </div>
        </div>';



$GLOBAL_LANGUAGE_OBJ['en']['privacy'] = 'Privacy';
$GLOBAL_LANGUAGE_OBJ['en']['cookies_policy'] = 'Cookies Policy';