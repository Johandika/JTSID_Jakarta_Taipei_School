<?php

$standardPage = [
    'notice' => 'Sample content for local testing. This is not an official school announcement.',
    'community_heading' => 'Our school community',
    'community_body' => 'Jakarta Taipei School brings together languages, cultures, and learning experiences in a diverse school environment.',
    'learning_heading' => 'Learning together',
    'learning_body' => 'School life includes academic learning, languages, student activities, and partnerships with parents.',
    'closing' => 'Detailed information for this page can be completed with school-approved content.',
];

$summary = 'Discover our community, learning, and life at Jakarta Taipei School.';

return [
    'about' => [
        'title' => 'About Jakarta Taipei School',
        'summary' => $summary,
        'seo_title' => 'About Jakarta Taipei School',
        'body' => $standardPage,
    ],
    'academics' => [
        'title' => 'Academics',
        'summary' => $summary,
        'seo_title' => 'Academics',
        'notice' => $standardPage['notice'],
        'mandarin_heading' => 'Mandarin',
        'mandarin_body' => 'Language, literacy, mathematics, science, and cultural learning from kindergarten through senior high school.',
        'indonesian_heading' => 'Indonesian, Pancasila, and Religions',
        'indonesian_body' => 'Connecting with the language and life of Indonesia through locally relevant learning.',
        'english_heading' => 'English',
        'english_body' => 'Building confidence in reading, writing, listening, and speaking.',
    ],
    'admissions' => [
        'title' => 'Admissions',
        'summary' => $summary,
        'seo_title' => 'Admissions',
        'notice' => $standardPage['notice'],
        'journey_heading' => 'Start your journey at JTS',
        'steps' => [
            'Contact admissions to discuss grade placement and availability.',
            'Arrange a campus visit.',
            'Request the current application form and document checklist.',
            'Follow the admissions process provided by the school.',
        ],
        'faq_heading' => 'Frequently asked questions',
        'fees_question' => 'How can I find current fees and dates?',
        'fees_answer' => 'Contact the admissions team for current, approved information.',
        'mandarin_question' => 'Is there a short-term Mandarin program?',
        'mandarin_prefix' => 'Explore the ',
        'mandarin_link' => 'Mandarin program',
        'mandarin_suffix' => ' and confirm class availability with the school.',
    ],
    'board_of_directors' => [
        'title' => 'Board of Directors',
        'summary' => $summary,
        'seo_title' => 'Board of Directors',
        'body' => $standardPage,
    ],
    'school_profile' => [
        'title' => 'School Profile',
        'summary' => $summary,
        'seo_title' => 'School Profile',
        'body' => $standardPage,
    ],
    'history' => [
        'title' => 'Our History',
        'summary' => $summary,
        'seo_title' => 'Our History',
        'body' => $standardPage,
    ],
    'parent_association' => [
        'title' => 'Parent Association',
        'summary' => $summary,
        'seo_title' => 'Parent Association',
        'body' => $standardPage,
    ],
    'contact' => [
        'title' => 'Location & Contact',
        'summary' => $summary,
        'seo_title' => 'Location & Contact',
        'notice' => $standardPage['notice'],
        'heading' => 'Visit Jakarta Taipei School',
        'address' => 'Kelapa Gading, North Jakarta, Indonesia.',
        'telephone' => 'Telephone: +62 21 452 3273',
        'contact_prefix' => 'Please contact the school before visiting. ',
        'contact_link' => 'Contact us on WhatsApp',
        'contact_suffix' => '.',
    ],
    'mandarin_program' => [
        'title' => 'Short-term Mandarin Program',
        'summary' => $summary,
        'seo_title' => 'Short-term Mandarin Program',
        'body' => $standardPage,
    ],
];
