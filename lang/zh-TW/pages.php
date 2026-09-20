<?php

$standardPage = [
    'notice' => '此為本機測試用範例內容，並非學校正式公告。',
    'community_heading' => '我們的學校社群',
    'community_body' => '雅加達臺灣學校在多元環境中連結語言、文化與學習經驗。',
    'learning_heading' => '共同學習',
    'learning_body' => '校園生活涵蓋學科、語言、學生活動，以及與家長的合作。',
    'closing' => '本頁詳細內容待學校核准後補充。',
];

$summary = '認識雅加達臺灣學校的社群、學習與校園生活。';

return [
    'about' => [
        'title' => '認識雅加達臺灣學校',
        'summary' => $summary,
        'seo_title' => '認識雅加達臺灣學校',
        'body' => $standardPage,
    ],
    'academics' => [
        'title' => '課程設計',
        'summary' => $summary,
        'seo_title' => '課程設計',
        'notice' => $standardPage['notice'],
        'mandarin_heading' => '華語課程',
        'mandarin_body' => '從幼兒園到高中，涵蓋語文、數學、科學及文化學習。',
        'indonesian_heading' => '印尼語、建國五原則與宗教',
        'indonesian_body' => '透過相關課程認識印尼語言與社會生活。',
        'english_heading' => '英語課程',
        'english_body' => '培養學生閱讀、寫作、聆聽與口語溝通的自信。',
    ],
    'admissions' => [
        'title' => '招生資訊',
        'summary' => $summary,
        'seo_title' => '招生資訊',
        'notice' => $standardPage['notice'],
        'journey_heading' => '開啟您在本校的旅程',
        'steps' => [
            '聯絡招生處，了解學制及名額。',
            '預約校園參觀。',
            '索取最新申請表及文件清單。',
            '依學校指引完成入學程序。',
        ],
        'faq_heading' => '常見問題',
        'fees_question' => '如何查詢費用與時間？',
        'fees_answer' => '請聯絡招生處取得最新正式資訊。',
        'mandarin_question' => '是否提供短期華語課程？',
        'mandarin_prefix' => '請參閱',
        'mandarin_link' => '華語課程',
        'mandarin_suffix' => '，並向學校確認開班資訊。',
    ],
    'board_of_directors' => [
        'title' => '理董事會',
        'summary' => $summary,
        'seo_title' => '理董事會',
        'body' => $standardPage,
    ],
    'school_profile' => [
        'title' => '學校概況',
        'summary' => $summary,
        'seo_title' => '學校概況',
        'body' => $standardPage,
    ],
    'history' => [
        'title' => '學校歷史',
        'summary' => $summary,
        'seo_title' => '學校歷史',
        'body' => $standardPage,
    ],
    'parent_association' => [
        'title' => '家長委員會',
        'summary' => $summary,
        'seo_title' => '家長委員會',
        'body' => $standardPage,
    ],
    'contact' => [
        'title' => '交通資訊與聯絡方式',
        'summary' => $summary,
        'seo_title' => '交通資訊與聯絡方式',
        'notice' => $standardPage['notice'],
        'heading' => '造訪雅加達臺灣學校',
        'address' => '印尼雅加達北區 Kelapa Gading。',
        'telephone' => '電話：+62 21 452 3273',
        'contact_prefix' => '參觀前請先聯絡學校。',
        'contact_link' => '透過 WhatsApp 聯絡',
        'contact_suffix' => '。',
    ],
    'mandarin_program' => [
        'title' => '短期華語課程',
        'summary' => $summary,
        'seo_title' => '短期華語課程',
        'body' => $standardPage,
    ],
];
