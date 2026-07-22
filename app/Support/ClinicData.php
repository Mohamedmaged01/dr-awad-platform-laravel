<?php

namespace App\Support;

/**
 * Presentational content ported 1:1 from the Next.js prototype's inline arrays.
 * These mirror the source components/pages exactly — see src/components/home/*
 * and src/app/{about,services,videos,blog,contact,booking,patient-portal}/page.tsx.
 */
class ClinicData
{
    /** HeroSection.tsx — stats */
    public static function heroStats(): array
    {
        return [
            ['value' => '20+', 'label' => 'سنة خبرة', 'icon' => 'award'],
            ['value' => '15000+', 'label' => 'عملية ناجحة', 'icon' => 'heart'],
            ['value' => '70%', 'label' => 'نسبة نجاح الحقن', 'icon' => 'sparkles'],
            ['value' => '25000+', 'label' => 'مريضة سعيدة', 'icon' => 'users'],
        ];
    }

    /** ServicesSection.tsx — home services grid */
    public static function homeServices(): array
    {
        return [
            [
                'icon' => 'baby',
                'title' => 'النساء والتوليد',
                'description' => 'رعاية شاملة للمرأة خلال فترة الحمل والولادة ومتابعة ما بعد الولادة مع أحدث التقنيات الطبية.',
                'features' => ['متابعة الحمل', 'الولادة الطبيعية', 'الولادة القيصرية', 'الحمل عالي الخطورة'],
                'href' => '/services/obstetrics-gynecology',
                'color' => 'from-pink-500 to-rose-500',
            ],
            [
                'icon' => 'microscope',
                'title' => 'الحقن المجهري وأطفال الأنابيب',
                'description' => 'أحدث تقنيات علاج العقم والإخصاب المساعد بنسب نجاح عالية تصل إلى 70%.',
                'features' => ['الحقن المجهري ICSI', 'أطفال الأنابيب IVF', 'التلقيح الصناعي', 'تجميد الأجنة'],
                'href' => '/services/ivf-icsi',
                'color' => 'from-blue-500 to-cyan-500',
            ],
            [
                'icon' => 'activity',
                'title' => 'جراحات المناظير',
                'description' => 'جراحات متقدمة بأقل تدخل جراحي وتعافي سريع باستخدام أحدث أجهزة المناظير.',
                'features' => ['منظار الرحم', 'منظار البطن', 'إزالة الألياف', 'علاج البطانة المهاجرة'],
                'href' => '/services/laparoscopy',
                'color' => 'from-emerald-500 to-teal-500',
            ],
            [
                'icon' => 'stethoscope',
                'title' => 'علاج أمراض النساء',
                'description' => 'تشخيص وعلاج جميع أمراض النساء بأحدث الأساليب العلاجية والجراحية.',
                'features' => ['تكيس المبايض', 'الأورام الليفية', 'اضطرابات الدورة', 'سن اليأس'],
                'href' => '/services/gynecology',
                'color' => 'from-purple-500 to-violet-500',
            ],
        ];
    }

    /** AboutSection.tsx — achievements */
    public static function aboutAchievements(): array
    {
        return [
            ['icon' => 'graduation-cap', 'label' => 'دكتوراه النساء والتوليد', 'desc' => 'جامعة القاهرة'],
            ['icon' => 'award', 'label' => 'زمالة الكلية الملكية', 'desc' => 'MRCOG - بريطانيا'],
            ['icon' => 'building', 'label' => 'استشاري بالمستشفيات الكبرى', 'desc' => 'أكثر من 20 عاماً'],
            ['icon' => 'globe', 'label' => 'عضو الجمعيات الدولية', 'desc' => 'ESHRE - ASRM'],
        ];
    }

    /** AboutSection.tsx — highlights (view renders first 4) */
    public static function aboutHighlights(): array
    {
        return [
            'أكثر من 15,000 عملية جراحية ناجحة',
            'نسب نجاح مرتفعة في الحقن المجهري تصل إلى 70%',
            'أحدث أجهزة المناظير والسونار رباعي الأبعاد',
            'فريق طبي متكامل على أعلى مستوى',
            'متابعة شخصية لكل حالة',
            'بيئة طبية مريحة وآمنة',
        ];
    }

    /** TestimonialsSection.tsx */
    public static function testimonials(): array
    {
        return [
            ['name' => 'سارة أحمد', 'location' => 'القاهرة', 'rating' => 5, 'service' => 'الحقن المجهري', 'content' => 'بعد سنوات من المحاولات، رزقني الله بتوأم بفضل الله ثم بفضل خبرة الدكتور محمد عوض. كان معنا في كل خطوة وأعطانا الأمل والثقة.'],
            ['name' => 'منى خالد', 'location' => 'الجيزة', 'rating' => 5, 'service' => 'متابعة الحمل', 'content' => 'تجربتي مع الدكتور محمد في متابعة الحمل كانت رائعة. المتابعة الدقيقة والاهتمام بكل التفاصيل جعلتني أشعر بالأمان.'],
            ['name' => 'نورا محمد', 'location' => 'الإسكندرية', 'rating' => 5, 'service' => 'جراحات المناظير', 'content' => 'أجريت عملية منظار رحم وكانت التجربة ممتازة. الدكتور شرح لي كل شيء بوضوح والتعافي كان سريعاً.'],
            ['name' => 'هالة عبدالله', 'location' => 'المنصورة', 'rating' => 5, 'service' => 'علاج تكيس المبايض', 'content' => 'الحمد لله رزقت بطفلتي الأولى بعد علاج تكيس المبايض. الدكتور محمد طبيب ماهر وإنسان راقي.'],
            ['name' => 'ريم سعيد', 'location' => 'طنطا', 'rating' => 5, 'service' => 'النساء والتوليد', 'content' => 'من أفضل أطباء النساء والتوليد. متابعة ممتازة، تعامل راقي، ونتائج مبهرة. أنصح به كل سيدة.'],
        ];
    }

    /** TestimonialsSection.tsx — bottom stats */
    public static function testimonialStats(): array
    {
        return [
            ['value' => '4.9', 'label' => 'تقييم المرضى'],
            ['value' => '25000+', 'label' => 'مريضة سعيدة'],
            ['value' => '98%', 'label' => 'نسبة الرضا'],
            ['value' => '5000+', 'label' => 'قصة نجاح'],
        ];
    }

    /** FAQSection.tsx */
    public static function faqs(): array
    {
        return [
            ['question' => 'ما هي نسبة نجاح الحقن المجهري لديكم؟', 'answer' => 'تصل نسب النجاح لدينا إلى 70% وهي من أعلى النسب في المنطقة. تعتمد النسبة على عدة عوامل منها: عمر الزوجة، جودة البويضات والحيوانات المنوية، وسبب العقم. نقوم بتقييم كل حالة بشكل فردي لتحديد أفضل خطة علاجية.'],
            ['question' => 'كم تستغرق دورة الحقن المجهري؟', 'answer' => 'تستغرق دورة الحقن المجهري الكاملة من 4 إلى 6 أسابيع تقريباً. تبدأ بالتحضير والفحوصات، ثم تنشيط المبايض لمدة 10-14 يوماً، يليها سحب البويضات وعملية الحقن في المختبر، ثم زرع الأجنة وانتظار نتيجة تحليل الحمل.'],
            ['question' => 'هل جراحات المناظير آمنة؟', 'answer' => 'نعم، جراحات المناظير تعتبر من أكثر الجراحات أماناً. نستخدم أحدث الأجهزة العالمية وتتميز بشقوق صغيرة جداً (5-10 ملم)، ألم أقل بعد العملية، تعافي سريع، وإقامة قصيرة بالمستشفى. معظم المريضات يعدن لنشاطهن الطبيعي خلال أيام قليلة.'],
            ['question' => 'ما هي خطوات متابعة الحمل لديكم؟', 'answer' => 'نقدم برنامج متابعة حمل شامل يتضمن: زيارات دورية شهرية (وأكثر في الأشهر الأخيرة)، سونار دوري لمتابعة نمو الجنين، تحاليل دورية للاطمئنان على صحة الأم والجنين، متابعة خاصة للحمل عالي الخطورة، ونصائح غذائية وصحية مستمرة.'],
            ['question' => 'هل تقدمون استشارات عن بعد؟', 'answer' => 'نعم، نقدم خدمة الاستشارات الإلكترونية عبر مكالمات الفيديو للحالات التي لا تستدعي فحصاً سريرياً. يمكنك حجز موعد استشارة عن بعد من خلال الموقع أو التواصل معنا عبر الواتساب.'],
            ['question' => 'كيف يمكنني حجز موعد؟', 'answer' => 'يمكنك حجز موعدك بسهولة عبر: نموذج الحجز على الموقع، الاتصال بنا على أرقام العيادة، التواصل عبر الواتساب، أو الحضور شخصياً لأحد فروعنا. سيتم التأكيد معك على الموعد المناسب.'],
        ];
    }

    /** BookingSection.tsx — simple branch options */
    public static function bookingSectionBranches(): array
    {
        return [
            ['value' => 'dokki', 'label' => 'فرع الدقي - القاهرة'],
            ['value' => 'maadi', 'label' => 'فرع المعادي - القاهرة'],
            ['value' => 'alex', 'label' => 'فرع الإسكندرية'],
        ];
    }

    /** BookingSection.tsx — simple service options */
    public static function bookingSectionServices(): array
    {
        return [
            ['value' => 'consultation', 'label' => 'كشف طبي'],
            ['value' => 'followup', 'label' => 'متابعة'],
            ['value' => 'pregnancy', 'label' => 'متابعة حمل'],
            ['value' => 'ivf', 'label' => 'استشارة حقن مجهري'],
            ['value' => 'surgery', 'label' => 'استشارة جراحية'],
        ];
    }

    /** about/page.tsx */
    public static function qualifications(): array
    {
        return [
            'بكالوريوس الطب والجراحة - جامعة القاهرة',
            'ماجستير أمراض النساء والتوليد - جامعة القاهرة',
            'دكتوراه أمراض النساء والتوليد - جامعة القاهرة',
            'زمالة الكلية الملكية البريطانية للنساء والتوليد (MRCOG)',
            'دبلوم الموجات فوق الصوتية في النساء والتوليد',
            'دبلوم جراحات المناظير المتقدمة',
        ];
    }

    public static function experience(): array
    {
        return [
            'استشاري النساء والتوليد بمستشفى القصر العيني',
            'استشاري الحقن المجهري بمركز الخصوبة الدولي',
            'استشاري جراحات المناظير بالمستشفيات الخاصة',
            'محاضر في الجمعية المصرية للخصوبة',
            'مدرب معتمد في جراحات المناظير',
        ];
    }

    public static function memberships(): array
    {
        return [
            'الجمعية الأوروبية للتناسل البشري والأجنة (ESHRE)',
            'الجمعية الأمريكية للطب التناسلي (ASRM)',
            'الكلية الملكية البريطانية للنساء والتوليد (RCOG)',
            'الجمعية المصرية لأمراض النساء والتوليد',
            'الجمعية المصرية للخصوبة',
            'الجمعية العربية للطب التناسلي',
        ];
    }

    /** about/page.tsx — hero achievement counters */
    public static function achievements(): array
    {
        return [
            ['value' => '20+', 'label' => 'سنة خبرة'],
            ['value' => '15000+', 'label' => 'عملية ناجحة'],
            ['value' => '70%', 'label' => 'نسبة نجاح الحقن'],
            ['value' => '25000+', 'label' => 'مريضة سعيدة'],
        ];
    }

    /** about/page.tsx — "Why Choose Us" */
    public static function whyChooseUs(): array
    {
        return [
            ['icon' => 'award', 'title' => 'خبرة واسعة', 'desc' => 'أكثر من 20 عاماً من الخبرة في المجال الطبي'],
            ['icon' => 'users', 'title' => 'فريق متكامل', 'desc' => 'فريق طبي متخصص على أعلى مستوى'],
            ['icon' => 'heart', 'title' => 'رعاية شخصية', 'desc' => 'متابعة شخصية لكل حالة بعناية فائقة'],
            ['icon' => 'calendar', 'title' => 'مواعيد مرنة', 'desc' => 'مواعيد متاحة طوال أيام الأسبوع'],
        ];
    }

    /** services/page.tsx — detailed services */
    public static function services(): array
    {
        return [
            [
                'id' => 'obstetrics-gynecology',
                'icon' => 'baby',
                'title' => 'النساء والتوليد',
                'image' => '/images/services/obstetrics.jpg',
                'description' => 'رعاية شاملة للمرأة خلال فترة الحمل والولادة ومتابعة ما بعد الولادة مع أحدث التقنيات الطبية والمعدات المتطورة.',
                'features' => ['متابعة الحمل الدورية', 'الحمل عالي الخطورة', 'الولادة الطبيعية', 'الولادة القيصرية', 'متابعة ما بعد الولادة', 'متابعة سن اليأس', 'علاج اضطرابات الدورة الشهرية'],
                'color' => 'from-pink-500 to-rose-500',
            ],
            [
                'id' => 'ivf-icsi',
                'icon' => 'microscope',
                'title' => 'الحقن المجهري وأطفال الأنابيب',
                'image' => '/images/services/ivf.jpg',
                'description' => 'أحدث تقنيات علاج العقم والإخصاب المساعد بنسب نجاح عالية تصل إلى 70%، مع متابعة دقيقة لكل مرحلة من مراحل العلاج.',
                'features' => ['الحقن المجهري (ICSI)', 'أطفال الأنابيب (IVF)', 'التلقيح الصناعي (IUI)', 'متابعة التبويض', 'تجميد الأجنة', 'تجميد البويضات', 'الفحوصات الوراثية للأجنة (PGS)', 'برامج علاج العقم المتكاملة'],
                'color' => 'from-blue-500 to-cyan-500',
            ],
            [
                'id' => 'laparoscopy',
                'icon' => 'activity',
                'title' => 'جراحات المناظير',
                'image' => '/images/services/laparoscopy.jpg',
                'description' => 'جراحات متقدمة بأقل تدخل جراحي وتعافي سريع باستخدام أحدث أجهزة المناظير الألمانية والسويسرية.',
                'features' => ['منظار الرحم التشخيصي', 'منظار الرحم العلاجي', 'منظار البطن النسائي', 'إزالة الألياف الرحمية', 'استئصال أكياس المبيض', 'علاج بطانة الرحم المهاجرة', 'فك الالتصاقات', 'الجراحات المحافظة على الخصوبة'],
                'color' => 'from-emerald-500 to-teal-500',
            ],
            [
                'id' => 'gynecology',
                'icon' => 'stethoscope',
                'title' => 'علاج أمراض النساء',
                'image' => '/images/services/gynecology.jpg',
                'description' => 'تشخيص وعلاج جميع أمراض النساء بأحدث الأساليب العلاجية والجراحية مع متابعة دورية لكل حالة.',
                'features' => ['تكيس المبايض', 'الأورام الليفية الرحمية', 'اضطرابات الدورة الشهرية', 'بطانة الرحم المهاجرة', 'التهابات الجهاز التناسلي', 'أكياس المبيض', 'متلازمة ما قبل الحيض', 'مشاكل سن اليأس'],
                'color' => 'from-purple-500 to-violet-500',
            ],
        ];
    }

    /** videos/page.tsx */
    public static function videos(): array
    {
        return [
            ['id' => 'vtSX64QP7ts', 'title' => 'معلومات مهمة عن الحقن المجهري', 'description' => 'شرح تفصيلي عن عملية الحقن المجهري ومراحلها وأهم النصائح للنجاح', 'category' => 'الحقن المجهري', 'duration' => '10:25', 'views' => '15K', 'date' => '2024-01-15'],
            ['id' => 'y02Pagf4lZ0', 'title' => 'متابعة الحمل - نصائح مهمة', 'description' => 'كل ما تحتاجين معرفته عن متابعة الحمل والفحوصات الدورية المطلوبة', 'category' => 'متابعة الحمل', 'duration' => '08:30', 'views' => '12K', 'date' => '2024-01-10'],
            ['id' => 'ykDMhc2Ma-0', 'title' => 'جراحات المناظير النسائية', 'description' => 'شرح عن جراحات المناظير ومميزاتها وفترة التعافي المتوقعة', 'category' => 'جراحات المناظير', 'duration' => '12:15', 'views' => '8K', 'date' => '2024-01-05'],
            ['id' => 'ZcqhfPNRZS4', 'title' => 'علاج تأخر الإنجاب', 'description' => 'أسباب تأخر الإنجاب وطرق العلاج المتاحة ونسب النجاح', 'category' => 'علاج العقم', 'duration' => '15:40', 'views' => '20K', 'date' => '2024-01-01'],
        ];
    }

    public static function videoCategories(): array
    {
        return ['الكل', 'الحقن المجهري', 'متابعة الحمل', 'جراحات المناظير', 'علاج العقم'];
    }

    /** blog/page.tsx */
    public static function articles(): array
    {
        return [
            ['id' => '1', 'title' => 'كل ما تحتاجين معرفته عن الحقن المجهري', 'excerpt' => 'الحقن المجهري (ICSI) هو أحد أكثر تقنيات الإخصاب المساعد تقدماً وفعالية. تعرفي على مراحل العملية ونسب النجاح والعوامل المؤثرة.', 'category' => 'الحقن المجهري', 'author' => 'د. محمد عوض', 'date' => '2024-01-15', 'readTime' => '8 دقائق'],
            ['id' => '2', 'title' => 'متابعة الحمل: دليلك الشامل للأشهر التسعة', 'excerpt' => 'متابعة الحمل المنتظمة ضرورية لصحة الأم والجنين. تعرفي على الفحوصات المطلوبة في كل مرحلة ونصائح للحفاظ على حمل صحي.', 'category' => 'متابعة الحمل', 'author' => 'د. محمد عوض', 'date' => '2024-01-12', 'readTime' => '10 دقائق'],
            ['id' => '3', 'title' => 'جراحات المناظير النسائية: تقنية متقدمة بأقل تدخل', 'excerpt' => 'جراحات المناظير توفر بديلاً آمناً وفعالاً للجراحات التقليدية. تعرفي على أنواعها ومميزاتها وفترة التعافي المتوقعة.', 'category' => 'جراحات المناظير', 'author' => 'د. محمد عوض', 'date' => '2024-01-10', 'readTime' => '6 دقائق'],
            ['id' => '4', 'title' => 'تكيس المبايض: الأسباب والأعراض وطرق العلاج', 'excerpt' => 'متلازمة تكيس المبايض من أكثر اضطرابات الغدد الصماء شيوعاً. تعرفي على أحدث طرق التشخيص والعلاج.', 'category' => 'أمراض النساء', 'author' => 'د. محمد عوض', 'date' => '2024-01-08', 'readTime' => '7 دقائق'],
            ['id' => '5', 'title' => 'الأورام الليفية الرحمية: كل ما تحتاجين معرفته', 'excerpt' => 'الأورام الليفية شائعة ولكن معظمها حميد. تعرفي على الأعراض وخيارات العلاج المتاحة بما في ذلك العلاج بالمنظار.', 'category' => 'أمراض النساء', 'author' => 'د. محمد عوض', 'date' => '2024-01-05', 'readTime' => '8 دقائق'],
            ['id' => '6', 'title' => 'تجميد البويضات: الحفاظ على الخصوبة للمستقبل', 'excerpt' => 'تجميد البويضات خيار متاح للنساء اللواتي يرغبن في تأجيل الإنجاب. تعرفي على العملية والعمر المناسب ونسب النجاح.', 'category' => 'الخصوبة', 'author' => 'د. محمد عوض', 'date' => '2024-01-02', 'readTime' => '6 دقائق'],
        ];
    }

    public static function articleCategories(): array
    {
        return ['الكل', 'الحقن المجهري', 'متابعة الحمل', 'جراحات المناظير', 'أمراض النساء', 'الخصوبة'];
    }

    /** contact/page.tsx — detailed branches */
    public static function contactBranches(): array
    {
        return [
            ['name' => 'فرع الدقي - القاهرة', 'address' => 'شارع التحرير، الدقي، الجيزة', 'phone' => '+20 123 456 7890', 'whatsapp' => '+20 123 456 7890', 'email' => 'dokki@dr-awad.com', 'hours' => 'السبت - الخميس: 9 ص - 9 م', 'mapUrl' => 'https://maps.google.com/?q=30.0444,31.2357'],
            ['name' => 'فرع المعادي - القاهرة', 'address' => 'شارع 9، المعادي، القاهرة', 'phone' => '+20 123 456 7891', 'whatsapp' => '+20 123 456 7891', 'email' => 'maadi@dr-awad.com', 'hours' => 'السبت - الخميس: 10 ص - 8 م', 'mapUrl' => 'https://maps.google.com/?q=29.9626,31.2497'],
            ['name' => 'فرع الإسكندرية', 'address' => 'سان ستيفانو، الإسكندرية', 'phone' => '+20 123 456 7892', 'whatsapp' => '+20 123 456 7892', 'email' => 'alex@dr-awad.com', 'hours' => 'السبت - الأربعاء: 10 ص - 6 م', 'mapUrl' => 'https://maps.google.com/?q=31.2435,29.9619'],
        ];
    }

    /** booking/page.tsx — branches with address */
    public static function bookingBranches(): array
    {
        return [
            ['value' => 'dokki', 'label' => 'فرع الدقي - القاهرة', 'address' => 'شارع التحرير، الدقي، الجيزة'],
            ['value' => 'maadi', 'label' => 'فرع المعادي - القاهرة', 'address' => 'شارع 9، المعادي، القاهرة'],
            ['value' => 'alex', 'label' => 'فرع الإسكندرية', 'address' => 'سان ستيفانو، الإسكندرية'],
        ];
    }

    /** booking/page.tsx — priced services */
    public static function bookingServices(): array
    {
        return [
            ['value' => 'consultation', 'label' => 'كشف طبي عام', 'price' => 500],
            ['value' => 'followup', 'label' => 'متابعة', 'price' => 300],
            ['value' => 'pregnancy', 'label' => 'متابعة حمل', 'price' => 400],
            ['value' => 'pregnancy-4d', 'label' => 'متابعة حمل + سونار رباعي الأبعاد', 'price' => 600],
            ['value' => 'ivf-consultation', 'label' => 'استشارة حقن مجهري', 'price' => 700],
            ['value' => 'surgery-consultation', 'label' => 'استشارة جراحية', 'price' => 600],
            ['value' => 'online', 'label' => 'استشارة أونلاين', 'price' => 400],
        ];
    }

    /** booking/page.tsx — time slots */
    public static function timeSlots(): array
    {
        return [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
            '19:00', '19:30', '20:00', '20:30',
        ];
    }

    /** patient-portal/page.tsx — demo dashboard data */
    public static function portalAppointments(): array
    {
        return [
            ['date' => '20 يناير 2024', 'time' => '10:00 ص', 'service' => 'متابعة حمل', 'branch' => 'فرع الدقي'],
            ['date' => '27 يناير 2024', 'time' => '11:30 ص', 'service' => 'سونار 4D', 'branch' => 'فرع الدقي'],
        ];
    }

    public static function portalRecords(): array
    {
        return [
            ['type' => 'تحاليل', 'name' => 'تحليل CBC', 'date' => '10 يناير 2024'],
            ['type' => 'سونار', 'name' => 'سونار الأسبوع 20', 'date' => '5 يناير 2024'],
            ['type' => 'وصفة', 'name' => 'فيتامينات الحمل', 'date' => '1 يناير 2024'],
            ['type' => 'تقرير', 'name' => 'تقرير المتابعة', 'date' => '25 ديسمبر 2023'],
        ];
    }
}
