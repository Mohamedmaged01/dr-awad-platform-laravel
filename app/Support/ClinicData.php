<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

/**
 * Presentational content ported from the Next.js prototype, now locale-aware.
 * Every method returns the Arabic or English variant based on the active locale.
 *
 * NOTE: the English medical copy below is a first-pass translation and should be
 * reviewed by the doctor/clinic for clinical accuracy before it goes live.
 */
class ClinicData
{
    /** Pick the Arabic or English string for the current locale. */
    private static function t(string $ar, string $en): string
    {
        return App::getLocale() === 'en' ? $en : $ar;
    }

    private static function isEn(): bool
    {
        return App::getLocale() === 'en';
    }

    /** ServicesSection.tsx — home services grid */
    public static function homeServices(): array
    {
        return [
            [
                'icon' => 'baby',
                'title' => self::t('النساء والتوليد', 'Obstetrics & Gynecology'),
                'description' => self::t(
                    'رعاية شاملة للمرأة خلال فترة الحمل والولادة ومتابعة ما بعد الولادة مع أحدث التقنيات الطبية.',
                    'Comprehensive care for women during pregnancy, delivery, and postpartum with the latest medical technologies.'
                ),
                'features' => self::isEn()
                    ? ['Pregnancy Follow-up', 'Natural Delivery', 'Cesarean Delivery', 'High-Risk Pregnancy']
                    : ['متابعة الحمل', 'الولادة الطبيعية', 'الولادة القيصرية', 'الحمل عالي الخطورة'],
                'href' => '/services/obstetrics-gynecology',
                'color' => 'from-pink-500 to-rose-500',
            ],
            [
                'icon' => 'microscope',
                'title' => self::t('الحقن المجهري وأطفال الأنابيب', 'IVF & ICSI'),
                'description' => self::t(
                    'أحدث تقنيات علاج العقم والإخصاب المساعد بنسب نجاح عالية تصل إلى 70%.',
                    'The latest infertility and assisted-reproduction technologies with success rates up to 70%.'
                ),
                'features' => self::isEn()
                    ? ['ICSI', 'IVF', 'IUI', 'Embryo Freezing']
                    : ['الحقن المجهري ICSI', 'أطفال الأنابيب IVF', 'التلقيح الصناعي', 'تجميد الأجنة'],
                'href' => '/services/ivf-icsi',
                'color' => 'from-blue-500 to-cyan-500',
            ],
            [
                'icon' => 'activity',
                'title' => self::t('جراحات المناظير', 'Laparoscopic Surgery'),
                'description' => self::t(
                    'جراحات متقدمة بأقل تدخل جراحي وتعافي سريع باستخدام أحدث أجهزة المناظير.',
                    'Advanced minimally invasive surgery with quick recovery using the latest laparoscopic equipment.'
                ),
                'features' => self::isEn()
                    ? ['Hysteroscopy', 'Laparoscopy', 'Fibroid Removal', 'Endometriosis Treatment']
                    : ['منظار الرحم', 'منظار البطن', 'إزالة الألياف', 'علاج البطانة المهاجرة'],
                'href' => '/services/laparoscopy',
                'color' => 'from-emerald-500 to-teal-500',
            ],
            [
                'icon' => 'stethoscope',
                'title' => self::t('علاج أمراض النساء', 'Gynecological Care'),
                'description' => self::t(
                    'تشخيص وعلاج جميع أمراض النساء بأحدث الأساليب العلاجية والجراحية.',
                    'Diagnosis and treatment of all gynecological conditions using the latest medical and surgical methods.'
                ),
                'features' => self::isEn()
                    ? ['PCOS', 'Fibroids', 'Menstrual Disorders', 'Menopause']
                    : ['تكيس المبايض', 'الأورام الليفية', 'اضطرابات الدورة', 'سن اليأس'],
                'href' => '/services/gynecology',
                'color' => 'from-purple-500 to-violet-500',
            ],
        ];
    }

    /** AboutSection.tsx — achievements */
    public static function aboutAchievements(): array
    {
        return [
            ['icon' => 'graduation-cap', 'label' => self::t('دكتوراه النساء والتوليد', 'PhD in Obstetrics & Gynecology'), 'desc' => self::t('جامعة القاهرة', 'Cairo University')],
            ['icon' => 'award', 'label' => self::t('زمالة الكلية الملكية', 'Royal College Fellowship'), 'desc' => self::t('MRCOG - بريطانيا', 'MRCOG - UK')],
            ['icon' => 'building', 'label' => self::t('استشاري بالمستشفيات الكبرى', 'Consultant at Major Hospitals'), 'desc' => self::t('أكثر من 20 عاماً', 'Over 20 years')],
            ['icon' => 'globe', 'label' => self::t('عضو الجمعيات الدولية', 'Member of International Societies'), 'desc' => self::t('ESHRE - ASRM', 'ESHRE - ASRM')],
        ];
    }

    /** AboutSection.tsx — highlights (view renders first 4) */
    public static function aboutHighlights(): array
    {
        return self::isEn()
            ? [
                'Over 15,000 successful surgical operations',
                'High IVF success rates reaching up to 70%',
                'Latest laparoscopy and 4D ultrasound equipment',
                'A fully integrated, top-tier medical team',
                'Personal follow-up for every case',
                'A comfortable and safe medical environment',
            ]
            : [
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
        return self::isEn()
            ? [
                ['name' => 'Sara Ahmed', 'location' => 'Cairo', 'rating' => 5, 'service' => 'IVF', 'content' => 'After years of trying, God blessed me with twins thanks to Dr. Mohamed Awad\'s expertise. He was with us every step of the way and gave us hope and confidence.'],
                ['name' => 'Mona Khaled', 'location' => 'Giza', 'rating' => 5, 'service' => 'Pregnancy Follow-up', 'content' => 'My experience with Dr. Mohamed for pregnancy follow-up was wonderful. The precise monitoring and attention to every detail made me feel safe.'],
                ['name' => 'Nora Mohamed', 'location' => 'Alexandria', 'rating' => 5, 'service' => 'Laparoscopic Surgery', 'content' => 'I had a hysteroscopy and the experience was excellent. The doctor explained everything clearly and recovery was quick.'],
                ['name' => 'Hala Abdullah', 'location' => 'Mansoura', 'rating' => 5, 'service' => 'PCOS Treatment', 'content' => 'Thank God, I was blessed with my first child after PCOS treatment. Dr. Mohamed is a skilled physician and a refined human being.'],
                ['name' => 'Reem Saeed', 'location' => 'Tanta', 'rating' => 5, 'service' => 'Obstetrics & Gynecology', 'content' => 'One of the best obstetricians and gynecologists. Excellent follow-up, refined manner, and impressive results. I recommend him to every woman.'],
            ]
            : [
                ['name' => 'سارة أحمد', 'location' => 'القاهرة', 'rating' => 5, 'service' => 'الحقن المجهري', 'content' => 'بعد سنوات من المحاولات، رزقني الله بتوأم بفضل الله ثم بفضل خبرة الدكتور محمد عوض. كان معنا في كل خطوة وأعطانا الأمل والثقة.'],
                ['name' => 'منى خالد', 'location' => 'الجيزة', 'rating' => 5, 'service' => 'متابعة الحمل', 'content' => 'تجربتي مع الدكتور محمد في متابعة الحمل كانت رائعة. المتابعة الدقيقة والاهتمام بكل التفاصيل جعلتني أشعر بالأمان.'],
                ['name' => 'نورا محمد', 'location' => 'الإسكندرية', 'rating' => 5, 'service' => 'جراحات المناظير', 'content' => 'أجريت عملية منظار رحم وكانت التجربة ممتازة. الدكتور شرح لي كل شيء بوضوح والتعافي كان سريعاً.'],
                ['name' => 'هالة عبدالله', 'location' => 'المنصورة', 'rating' => 5, 'service' => 'علاج تكيس المبايض', 'content' => 'الحمد لله رزقت بطفلتي الأولى بعد علاج تكيس المبايض. الدكتور محمد طبيب ماهر وإنسان راقي.'],
                ['name' => 'ريم سعيد', 'location' => 'طنطا', 'rating' => 5, 'service' => 'النساء والتوليد', 'content' => 'من أفضل أطباء النساء والتوليد. متابعة ممتازة، تعامل راقي، ونتائج مبهرة. أنصح به كل سيدة.'],
            ];
    }

    /** FAQSection.tsx */
    public static function faqs(): array
    {
        return self::isEn()
            ? [
                ['question' => 'What is your IVF success rate?', 'answer' => 'Our success rates reach up to 70%, among the highest in the region. The rate depends on several factors: the wife\'s age, the quality of eggs and sperm, and the cause of infertility. We assess each case individually to determine the best treatment plan.'],
                ['question' => 'How long does an IVF cycle take?', 'answer' => 'A full IVF cycle takes roughly 4 to 6 weeks. It begins with preparation and tests, then ovarian stimulation for 10-14 days, followed by egg retrieval and fertilization in the lab, then embryo transfer and waiting for the pregnancy test result.'],
                ['question' => 'Is laparoscopic surgery safe?', 'answer' => 'Yes, laparoscopic surgery is among the safest procedures. We use the latest global equipment, and it features very small incisions (5-10 mm), less post-operative pain, quick recovery, and a short hospital stay. Most patients return to their normal activities within a few days.'],
                ['question' => 'What are your pregnancy follow-up steps?', 'answer' => 'We offer a comprehensive pregnancy follow-up program including: regular monthly visits (and more in the final months), periodic ultrasounds to monitor fetal growth, regular tests to check the health of mother and baby, special monitoring for high-risk pregnancies, and ongoing nutritional and health advice.'],
                ['question' => 'Do you offer remote consultations?', 'answer' => 'Yes, we offer electronic consultations via video calls for cases that do not require a physical examination. You can book a remote consultation appointment through the website or contact us via WhatsApp.'],
                ['question' => 'How can I book an appointment?', 'answer' => 'You can easily book your appointment via: the booking form on the website, calling the clinic numbers, contacting us on WhatsApp, or visiting one of our branches in person. Your suitable appointment will be confirmed with you.'],
            ]
            : [
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
            ['value' => 'beni-mazar', 'label' => self::t('فرع بني مزار', 'Beni Mazar Branch')],
            ['value' => 'sheikh-zayed', 'label' => self::t('فرع الشيخ زايد', 'Sheikh Zayed Branch')],
        ];
    }

    /** BookingSection.tsx — simple service options */
    public static function bookingSectionServices(): array
    {
        return [
            ['value' => 'consultation', 'label' => self::t('كشف طبي', 'Medical Consultation')],
            ['value' => 'followup', 'label' => self::t('متابعة', 'Follow-up')],
            ['value' => 'pregnancy', 'label' => self::t('متابعة حمل', 'Pregnancy Follow-up')],
            ['value' => 'ivf', 'label' => self::t('استشارة حقن مجهري', 'IVF Consultation')],
            ['value' => 'surgery', 'label' => self::t('استشارة جراحية', 'Surgical Consultation')],
        ];
    }

    /** about/page.tsx */
    public static function qualifications(): array
    {
        return self::isEn()
            ? [
                'Bachelor of Medicine and Surgery - Cairo University',
                'Master\'s in Obstetrics & Gynecology - Cairo University',
                'PhD in Obstetrics & Gynecology - Cairo University',
                'Fellowship of the Royal College of Obstetricians and Gynaecologists (MRCOG)',
                'Diploma in Obstetric & Gynecologic Ultrasound',
                'Diploma in Advanced Laparoscopic Surgery',
            ]
            : [
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
        return self::isEn()
            ? [
                'Consultant of Obstetrics & Gynecology at Kasr Al-Ainy Hospital',
                'IVF Consultant at the International Fertility Center',
                'Laparoscopic Surgery Consultant at private hospitals',
                'Lecturer at the Egyptian Fertility Society',
                'Certified trainer in laparoscopic surgery',
            ]
            : [
                'استشاري النساء والتوليد بمستشفى القصر العيني',
                'استشاري الحقن المجهري بمركز الخصوبة الدولي',
                'استشاري جراحات المناظير بالمستشفيات الخاصة',
                'محاضر في الجمعية المصرية للخصوبة',
                'مدرب معتمد في جراحات المناظير',
            ];
    }

    public static function memberships(): array
    {
        return self::isEn()
            ? [
                'European Society of Human Reproduction and Embryology (ESHRE)',
                'American Society for Reproductive Medicine (ASRM)',
                'Royal College of Obstetricians and Gynaecologists (RCOG)',
                'Egyptian Society of Obstetrics and Gynecology',
                'Egyptian Fertility Society',
                'Arab Society of Reproductive Medicine',
            ]
            : [
                'الجمعية الأوروبية للتناسل البشري والأجنة (ESHRE)',
                'الجمعية الأمريكية للطب التناسلي (ASRM)',
                'الكلية الملكية البريطانية للنساء والتوليد (RCOG)',
                'الجمعية المصرية لأمراض النساء والتوليد',
                'الجمعية المصرية للخصوبة',
                'الجمعية العربية للطب التناسلي',
            ];
    }

    /** about/page.tsx — "Why Choose Us" */
    public static function whyChooseUs(): array
    {
        return [
            ['icon' => 'award', 'title' => self::t('خبرة واسعة', 'Extensive Experience'), 'desc' => self::t('أكثر من 20 عاماً من الخبرة في المجال الطبي', 'Over 20 years of experience in the medical field')],
            ['icon' => 'users', 'title' => self::t('فريق متكامل', 'Integrated Team'), 'desc' => self::t('فريق طبي متخصص على أعلى مستوى', 'A top-tier specialized medical team')],
            ['icon' => 'heart', 'title' => self::t('رعاية شخصية', 'Personal Care'), 'desc' => self::t('متابعة شخصية لكل حالة بعناية فائقة', 'Personal follow-up for every case with great care')],
            ['icon' => 'calendar', 'title' => self::t('مواعيد مرنة', 'Flexible Appointments'), 'desc' => self::t('مواعيد متاحة طوال أيام الأسبوع', 'Appointments available all week')],
        ];
    }

    /** services/page.tsx — detailed services */
    public static function services(): array
    {
        return [
            [
                'id' => 'obstetrics-gynecology',
                'icon' => 'baby',
                'title' => self::t('النساء والتوليد', 'Obstetrics & Gynecology'),
                'image' => '/images/services/obstetrics.jpg',
                'description' => self::t(
                    'رعاية شاملة للمرأة خلال فترة الحمل والولادة ومتابعة ما بعد الولادة مع أحدث التقنيات الطبية والمعدات المتطورة.',
                    'Comprehensive care for women during pregnancy, delivery, and postpartum with the latest medical technologies and advanced equipment.'
                ),
                'features' => self::isEn()
                    ? ['Routine Pregnancy Follow-up', 'High-Risk Pregnancy', 'Natural Delivery', 'Cesarean Delivery', 'Postpartum Care', 'Menopause Care', 'Menstrual Disorder Treatment']
                    : ['متابعة الحمل الدورية', 'الحمل عالي الخطورة', 'الولادة الطبيعية', 'الولادة القيصرية', 'متابعة ما بعد الولادة', 'متابعة سن اليأس', 'علاج اضطرابات الدورة الشهرية'],
                'color' => 'from-pink-500 to-rose-500',
            ],
            [
                'id' => 'ivf-icsi',
                'icon' => 'microscope',
                'title' => self::t('الحقن المجهري وأطفال الأنابيب', 'IVF & ICSI'),
                'image' => '/images/services/ivf.jpg',
                'description' => self::t(
                    'أحدث تقنيات علاج العقم والإخصاب المساعد بنسب نجاح عالية تصل إلى 70%، مع متابعة دقيقة لكل مرحلة من مراحل العلاج.',
                    'The latest infertility and assisted-reproduction technologies with success rates up to 70%, with precise monitoring at every treatment stage.'
                ),
                'features' => self::isEn()
                    ? ['ICSI', 'IVF', 'IUI', 'Ovulation Tracking', 'Embryo Freezing', 'Egg Freezing', 'Preimplantation Genetic Screening (PGS)', 'Integrated infertility treatment programs']
                    : ['الحقن المجهري (ICSI)', 'أطفال الأنابيب (IVF)', 'التلقيح الصناعي (IUI)', 'متابعة التبويض', 'تجميد الأجنة', 'تجميد البويضات', 'الفحوصات الوراثية للأجنة (PGS)', 'برامج علاج العقم المتكاملة'],
                'color' => 'from-blue-500 to-cyan-500',
            ],
            [
                'id' => 'laparoscopy',
                'icon' => 'activity',
                'title' => self::t('جراحات المناظير', 'Laparoscopic Surgery'),
                'image' => '/images/services/laparoscopy.jpg',
                'description' => self::t(
                    'جراحات متقدمة بأقل تدخل جراحي وتعافي سريع باستخدام أحدث أجهزة المناظير الألمانية والسويسرية.',
                    'Advanced minimally invasive surgery with quick recovery using the latest German and Swiss laparoscopic equipment.'
                ),
                'features' => self::isEn()
                    ? ['Diagnostic Hysteroscopy', 'Operative Hysteroscopy', 'Gynecologic Laparoscopy', 'Uterine Fibroid Removal', 'Ovarian Cyst Removal', 'Endometriosis Treatment', 'Adhesion Lysis', 'Fertility-preserving surgery']
                    : ['منظار الرحم التشخيصي', 'منظار الرحم العلاجي', 'منظار البطن النسائي', 'إزالة الألياف الرحمية', 'استئصال أكياس المبيض', 'علاج بطانة الرحم المهاجرة', 'فك الالتصاقات', 'الجراحات المحافظة على الخصوبة'],
                'color' => 'from-emerald-500 to-teal-500',
            ],
            [
                'id' => 'gynecology',
                'icon' => 'stethoscope',
                'title' => self::t('علاج أمراض النساء', 'Gynecological Care'),
                'image' => '/images/services/gynecology.jpg',
                'description' => self::t(
                    'تشخيص وعلاج جميع أمراض النساء بأحدث الأساليب العلاجية والجراحية مع متابعة دورية لكل حالة.',
                    'Diagnosis and treatment of all gynecological conditions using the latest medical and surgical methods with regular follow-up for each case.'
                ),
                'features' => self::isEn()
                    ? ['PCOS', 'Uterine Fibroids', 'Menstrual Disorders', 'Endometriosis', 'Reproductive Tract Infections', 'Ovarian Cysts', 'Premenstrual Syndrome', 'Menopause Issues']
                    : ['تكيس المبايض', 'الأورام الليفية الرحمية', 'اضطرابات الدورة الشهرية', 'بطانة الرحم المهاجرة', 'التهابات الجهاز التناسلي', 'أكياس المبيض', 'متلازمة ما قبل الحيض', 'مشاكل سن اليأس'],
                'color' => 'from-purple-500 to-violet-500',
            ],
        ];
    }

    /** videos/page.tsx */
    public static function videos(): array
    {
        return self::isEn()
            ? [
                ['id' => 'vtSX64QP7ts', 'title' => 'Important Information About IVF', 'description' => 'A detailed explanation of the IVF process, its stages, and top tips for success', 'category' => 'IVF', 'duration' => '10:25', 'views' => '15K', 'date' => '2024-01-15'],
                ['id' => 'y02Pagf4lZ0', 'title' => 'Pregnancy Follow-up - Important Tips', 'description' => 'Everything you need to know about pregnancy follow-up and the required routine tests', 'category' => 'Pregnancy Follow-up', 'duration' => '08:30', 'views' => '12K', 'date' => '2024-01-10'],
                ['id' => 'ykDMhc2Ma-0', 'title' => 'Gynecologic Laparoscopic Surgery', 'description' => 'An explanation of laparoscopic surgery, its advantages, and the expected recovery period', 'category' => 'Laparoscopic Surgery', 'duration' => '12:15', 'views' => '8K', 'date' => '2024-01-05'],
                ['id' => 'ZcqhfPNRZS4', 'title' => 'Treating Delayed Conception', 'description' => 'Causes of delayed conception, available treatment options, and success rates', 'category' => 'Infertility Treatment', 'duration' => '15:40', 'views' => '20K', 'date' => '2024-01-01'],
            ]
            : [
                ['id' => 'vtSX64QP7ts', 'title' => 'معلومات مهمة عن الحقن المجهري', 'description' => 'شرح تفصيلي عن عملية الحقن المجهري ومراحلها وأهم النصائح للنجاح', 'category' => 'الحقن المجهري', 'duration' => '10:25', 'views' => '15K', 'date' => '2024-01-15'],
                ['id' => 'y02Pagf4lZ0', 'title' => 'متابعة الحمل - نصائح مهمة', 'description' => 'كل ما تحتاجين معرفته عن متابعة الحمل والفحوصات الدورية المطلوبة', 'category' => 'متابعة الحمل', 'duration' => '08:30', 'views' => '12K', 'date' => '2024-01-10'],
                ['id' => 'ykDMhc2Ma-0', 'title' => 'جراحات المناظير النسائية', 'description' => 'شرح عن جراحات المناظير ومميزاتها وفترة التعافي المتوقعة', 'category' => 'جراحات المناظير', 'duration' => '12:15', 'views' => '8K', 'date' => '2024-01-05'],
                ['id' => 'ZcqhfPNRZS4', 'title' => 'علاج تأخر الإنجاب', 'description' => 'أسباب تأخر الإنجاب وطرق العلاج المتاحة ونسب النجاح', 'category' => 'علاج العقم', 'duration' => '15:40', 'views' => '20K', 'date' => '2024-01-01'],
            ];
    }

    public static function videoCategories(): array
    {
        return self::isEn()
            ? ['All', 'IVF', 'Pregnancy Follow-up', 'Laparoscopic Surgery', 'Infertility Treatment']
            : ['الكل', 'الحقن المجهري', 'متابعة الحمل', 'جراحات المناظير', 'علاج العقم'];
    }

    /** blog/page.tsx */
    public static function articles(): array
    {
        return self::isEn()
            ? [
                ['id' => '1', 'title' => 'Everything You Need to Know About IVF', 'excerpt' => 'ICSI is one of the most advanced and effective assisted-reproduction techniques. Learn about the stages of the process, success rates, and influencing factors.', 'category' => 'IVF', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-15', 'readTime' => '8 min'],
                ['id' => '2', 'title' => 'Pregnancy Follow-up: Your Complete Guide to the Nine Months', 'excerpt' => 'Regular pregnancy follow-up is essential for the health of mother and baby. Learn about the tests required at each stage and tips for a healthy pregnancy.', 'category' => 'Pregnancy Follow-up', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-12', 'readTime' => '10 min'],
                ['id' => '3', 'title' => 'Gynecologic Laparoscopy: Advanced, Minimally Invasive Technology', 'excerpt' => 'Laparoscopic surgery offers a safe and effective alternative to traditional surgery. Learn about its types, advantages, and expected recovery period.', 'category' => 'Laparoscopic Surgery', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-10', 'readTime' => '6 min'],
                ['id' => '4', 'title' => 'PCOS: Causes, Symptoms, and Treatment', 'excerpt' => 'Polycystic ovary syndrome is one of the most common endocrine disorders. Learn about the latest diagnosis and treatment methods.', 'category' => 'Gynecology', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-08', 'readTime' => '7 min'],
                ['id' => '5', 'title' => 'Uterine Fibroids: Everything You Need to Know', 'excerpt' => 'Fibroids are common but most are benign. Learn about the symptoms and available treatment options, including laparoscopic treatment.', 'category' => 'Gynecology', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-05', 'readTime' => '8 min'],
                ['id' => '6', 'title' => 'Egg Freezing: Preserving Fertility for the Future', 'excerpt' => 'Egg freezing is an option for women who wish to delay childbearing. Learn about the process, the suitable age, and success rates.', 'category' => 'Fertility', 'author' => 'Dr. Mohamed Awad', 'date' => '2024-01-02', 'readTime' => '6 min'],
            ]
            : [
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
        return self::isEn()
            ? ['All', 'IVF', 'Pregnancy Follow-up', 'Laparoscopic Surgery', 'Gynecology', 'Fertility']
            : ['الكل', 'الحقن المجهري', 'متابعة الحمل', 'جراحات المناظير', 'أمراض النساء', 'الخصوبة'];
    }

    /** contact/page.tsx — detailed branches */
    public static function contactBranches(): array
    {
        return [
            ['name' => self::t('فرع بني مزار', 'Beni Mazar Branch'), 'address' => self::t('شارع موقف صندفا - بجوار مسجد سيدنا حمزة', 'Sandafa Station St. - next to Sayyidna Hamza Mosque'), 'phone' => '01005078266', 'whatsapp' => '01005078266', 'email' => 'info@dr-awad.com', 'hours' => self::t('يومياً من 9 ص - 10 م', 'Daily 9 AM - 10 PM'), 'mapUrl' => 'https://maps.google.com/?q=بني+مزار'],
            ['name' => self::t('فرع الشيخ زايد', 'Sheikh Zayed Branch'), 'address' => self::t('الشيخ زايد - القاهرة', 'Sheikh Zayed - Cairo'), 'phone' => '01005078266', 'whatsapp' => '01005078266', 'email' => 'info@dr-awad.com', 'hours' => self::t('يومياً من 9 ص - 10 م', 'Daily 9 AM - 10 PM'), 'mapUrl' => 'https://maps.google.com/?q=الشيخ+زايد'],
        ];
    }

    /** booking/page.tsx — branches with address */
    public static function bookingBranches(): array
    {
        return [
            ['value' => 'beni-mazar', 'label' => self::t('فرع بني مزار', 'Beni Mazar Branch'), 'address' => self::t('شارع موقف صندفا - بجوار مسجد سيدنا حمزة', 'Sandafa Station St. - next to Sayyidna Hamza Mosque')],
            ['value' => 'sheikh-zayed', 'label' => self::t('فرع الشيخ زايد', 'Sheikh Zayed Branch'), 'address' => self::t('الشيخ زايد - القاهرة', 'Sheikh Zayed - Cairo')],
        ];
    }

    /** booking/page.tsx — priced services */
    public static function bookingServices(): array
    {
        return [
            ['value' => 'consultation', 'label' => self::t('كشف طبي عام', 'General Consultation'), 'price' => 500],
            ['value' => 'followup', 'label' => self::t('متابعة', 'Follow-up'), 'price' => 300],
            ['value' => 'pregnancy', 'label' => self::t('متابعة حمل', 'Pregnancy Follow-up'), 'price' => 400],
            ['value' => 'pregnancy-4d', 'label' => self::t('متابعة حمل + سونار رباعي الأبعاد', 'Pregnancy Follow-up + 4D Ultrasound'), 'price' => 600],
            ['value' => 'ivf-consultation', 'label' => self::t('استشارة حقن مجهري', 'IVF Consultation'), 'price' => 700],
            ['value' => 'surgery-consultation', 'label' => self::t('استشارة جراحية', 'Surgical Consultation'), 'price' => 600],
            ['value' => 'online', 'label' => self::t('استشارة أونلاين', 'Online Consultation'), 'price' => 400],
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
        return self::isEn()
            ? [
                ['date' => '20 January 2024', 'time' => '10:00 AM', 'service' => 'Pregnancy Follow-up', 'branch' => 'Beni Mazar Branch'],
                ['date' => '27 January 2024', 'time' => '11:30 AM', 'service' => '4D Ultrasound', 'branch' => 'Beni Mazar Branch'],
            ]
            : [
                ['date' => '20 يناير 2024', 'time' => '10:00 ص', 'service' => 'متابعة حمل', 'branch' => 'فرع بني مزار'],
                ['date' => '27 يناير 2024', 'time' => '11:30 ص', 'service' => 'سونار 4D', 'branch' => 'فرع بني مزار'],
            ];
    }

    public static function portalRecords(): array
    {
        return self::isEn()
            ? [
                ['type' => 'Lab Tests', 'name' => 'CBC Test', 'date' => '10 January 2024'],
                ['type' => 'Ultrasound', 'name' => 'Week 20 Ultrasound', 'date' => '5 January 2024'],
                ['type' => 'Prescription', 'name' => 'Pregnancy Vitamins', 'date' => '1 January 2024'],
                ['type' => 'Report', 'name' => 'Follow-up Report', 'date' => '25 December 2023'],
            ]
            : [
                ['type' => 'تحاليل', 'name' => 'تحليل CBC', 'date' => '10 يناير 2024'],
                ['type' => 'سونار', 'name' => 'سونار الأسبوع 20', 'date' => '5 يناير 2024'],
                ['type' => 'وصفة', 'name' => 'فيتامينات الحمل', 'date' => '1 يناير 2024'],
                ['type' => 'تقرير', 'name' => 'تقرير المتابعة', 'date' => '25 ديسمبر 2023'],
            ];
    }
}
