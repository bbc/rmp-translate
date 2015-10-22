<?php

namespace RMP\Translate;

/**
 * Class DateCorrection
 *
 * Fixes translations of Dates provided by PHP Date helpers
 * Generally Spelling mistakes. Use after you have created
 * your date strings
 * $dateHelper = new \RMP\Translate\DateCorrection();
 * $string = $dateHelper->fixSpelling($string, $locale);
 *
 * Original source of corrections:
 * https://docs.google.com/spreadsheets/d/10F58jZ73Mdq5Ej8-rzc1Rq-ZT4oiAnW_3hQpVFISLB8/edit#gid=0
 *
 * @package RMP\Translate
 */
class DateCorrection
{
    /**
     * @param $string
     * @param $locale
     * @return string
     */
    public function fixSpelling($string, $locale)
    {
        // standardise to the dash separated locale
        $locale = str_replace('_','-', $locale);

        // if the full match can't be found, reduce to the language code
        if (!array_key_exists($locale, $this->corrections)) {
            $locale = substr($locale, 0, 2);
        }

        // re-check if it exists
        if (array_key_exists($locale, $this->corrections)) {
            $string = strtr($string, $this->corrections[$locale]);
        }

        return $string;
    }

    private $corrections = array(

        // bn (Bengali)
        'bn' => array(
            'জানুয়ারী' => 'জানুয়ারি', // January
            'ফেব্রুয়ারী' => 'ফেব্রুয়ারি', // February
            'আগস্ট' => 'অগাস্ট', // August
        ),

        // cy (Welsh)
        'cy' => array(
            'Gorffenaf' => 'Gorffennaf', // July
        ),

        // ha (Housa)
        'ha' => array(
            'Afrilu' => 'Aprilu', // April
            'Augusta' => 'Agusta', // August
        ),

        // hi (Hindi)
        'hi' => array(
            'सितम्बर' => 'सितंबर', // September
            'नवम्बर' => 'नवंबर', // November
            'दिसम्बर' => 'दिसंबर', // December
        ),

        // ne (Nepali)
        'ne' => array(
            'अप्रिल' => 'एप्रिल', // April
            'अगस्त' => 'अगस्ट', // August
        ),

        // rw-RW (Gahuza)
        // not available in PHP, so must use the English keys
        // which is limited to short month support
        'rw' => array(
            'January' => 'mut.', // January
            'February' => 'gas.', // February
            'March' => 'wer.', // March
            'April' => 'mat.', // April
            'May' => 'gic.', // May
            'June' => 'kam.', // June
            'July' => 'nya.', // July
            'August' => 'kan.', // August
            'September' => 'nze.', // September
            'October' => 'ukw.', // October
            'November' => 'ugu.', // November
            'December' => 'uku.', // December,

            'Jan' => 'mut.', // January
            'Feb' => 'gas.', // February
            'Mar' => 'wer.', // March
            'Apr' => 'mat.', // April
            // May already covered above
            'Jun' => 'kam.', // June
            'Jul' => 'nya.', // July
            'Aug' => 'kan.', // August
            'Sep' => 'nze.', // September
            'Oct' => 'ukw.', // October
            'Nov' => 'ugu.', // November
            'Decr' => 'uku.', // December

            'Monday' => 'Kuwa mbere', // Monday
            'Tuesday' => 'Kuwa kabiri', // Tuesday
            'Wednesday' => 'Kuwa gatatu', // Wednesday
            'Thursday' => 'Kuwa kane', // Thursday
            'Friday' => 'Kuwa gatanu', // Friday
            'Saturday' => 'Kuwa gatandatu', // Saturday
            'Sunday' => 'Ku cyumweru', // Sunday

            'Mon' => 'mbe.', // Monday
            'Tue' => 'kab.', // Tuesday
            'Wed' => 'gtu.', // Wednesday
            'Thu' => 'kan.', // Thursday
            'Fri' => 'gnu.', // Friday
            'Sat' => 'gnd.', // Saturday
            'Sun' => 'cyu.' // Sunday
        ),

        // si (Sinhala)
        'si' => array(
            'ජනවාර' => 'ජනවාරි', // January
            'පෙබරවාර' => 'පෙබරවාරි', // February
            'මාර්ත' => 'මාර්තු', // March
            'ජූන' => 'ජුනි ', // June
        ),

        // so (Somali)
        'so' => array(
            'Bisha Koobaad' => 'Jannaayo', // January
            'Bisha Labaad' => 'Febraayo', // February
            'Bisha Saddexaad' => 'Maarso', // March
            'Bisha Afraad' => 'Abriil', // April
            'Bisha Shanaad' => 'Maajo', // May
            'Bisha Lixaad' => 'Juunyo', // June
            'Bisha Todobaad' => 'Luulyo', // July
            'Bisha Sideedaad' => 'Agoosto', // August
            'Bisha Sagaalaad' => 'Sebtembar', // September
            'Bisha Tobnaad' => 'Oktoobar', // October
            'Bisha Kow iyo Tobnaad' => 'Nofembar', // November
            'Bisha Laba iyo Tobnaad' => 'Disembar', // December
        ),

        // sw (Swahili)
        'sw' => array(
            'Desemba' => 'Disemba', // December
        ),

        // ur (Urdu)
        // note right-to-left language so the display order of key => value
        // may differ or be unusual depending on your editor
        'ur' => array(
            'مار چ' => 'مارچ', // March
        ),

        // uz (Uzbek)
        'uz' => array(
            'Муҳаррам' => 'Январ', // January
            'Сафар' => 'Феврал', // February
            'Рабиул-аввал' => 'Март', // March
            'Рабиул-охир' => 'Апрел', // April
            'Жумодиул-уло' => 'Май', // May
            'Жумодиул-ухро' => 'Июн', // June
            'Ражаб' => 'Июл', // July
            'Шаъбон' => 'Август', // August
            'Рамазон' => 'Сентябр', // September
            'Шаввол' => 'Октябр', // October
            'Зил-қаъда' => 'Ноябр', // November
            'Зил-ҳижжа' => 'Декабр', // December
        ),

        // vi (Vietnamese)
        'vi' => array(
            'tháng một' => 'tháng 1',  // January
            'tháng hai' => 'tháng 2', // February
            'tháng ba' => 'tháng 3', // March
            'tháng tư' => 'tháng 4', // April
            'tháng năm' => 'tháng 5', // May
            'tháng sáu' => 'tháng 6', // June
            'tháng bảy' => 'tháng 7', // July
            'tháng tám' => 'tháng 8', // August
            'tháng chín' => 'tháng 9', // September
            'tháng mười' => 'tháng 10', // October
            'tháng mười một' => 'tháng 11', // November
            'tháng mười hai' => 'tháng 12', // December
        )
    );
}