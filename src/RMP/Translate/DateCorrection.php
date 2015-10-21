<?php

namespace RMP\Translate;

/**
 * Class DateCorrection
 *
 * Fixes translations of Dates provided by PHP Date helpers
 * Generally Spelling mistakes. Use after you have created
 * your date strings
 * $dateHelper = new \RMP\Translate\DateCorrection();
 * $string = $dateHelper->fixSpelling($string);
 *
 * Original source of corrections:
 * https://docs.google.com/spreadsheets/d/10F58jZ73Mdq5Ej8-rzc1Rq-ZT4oiAnW_3hQpVFISLB8/edit#gid=0
 *
 * @package RMP\Translate
 */
class DateCorrection
{
    public function fixSpelling($string)
    {
        $corrections = array(

            // bn (Bengali)
            'জানুয়ারী' => 'জানুয়ারি', // January
            'ফেব্রুয়ারী' => 'ফেব্রুয়ারি', // February
            'আগস্ট' => 'অগাস্ট', // August

            // cy (Welsh)
            'Gorffenaf' => 'Gorffennaf', // July

            // ha (Housa)
            'Afrilu' => 'Aprilu', // April
            'Augusta' => 'Agusta', // August

            // hi (Hindi)
            'सितम्बर'=>'सितंबर', // September
            'नवम्बर'=>'नवंबर', // November
            'दिसम्बर'=>'दिसंबर', // December

            // ne (Nepali)
            'अप्रिल'=> 'एप्रिल', // April
            'अगस्त'=> 'अगस्ट', // August

            // rw_RW (Gahuza)
            'Mutarama' => 'Ukwa mbere', // January
            'Gashyantare' => 'Ukwa kabiri', // February
            'Werurwe' => 'Ukwa gatatu', // March
            'Mata' => 'Ukwa kane', // April
            'Gicuransi' => 'Ukwa gatanu', // May
            'Kamena' => 'Ukwa gatandatu', // June
            'Nyakanga' => 'Ukw’indwi', // July
            'Kanama' => 'Ukw’umunani', // August
            'Nzeli' => 'Ukw’icenda', // September
            'Ukwakira' => 'Ukw’icumi', // October
            'Ugushyingo' => 'Ukw’icumi na rimwe', // November
            'Ukuboza' => 'Ukw’icumi na kabiri', // December

            // si (Sinhala)
            'ජනවාර' => 'ජනවාරි', // January
            'පෙබරවාර' => 'පෙබරවාරි', // February
            'මාර්ත' => 'මාර්තු', // March
            'ජූන' => 'ජුනි ', // June

            // so (Somali)
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

            // sw (Swahili)
            'Desemba' => 'Disemba', // December

            // ur (Urdu)
            'مار چ' => 'مارچ', // March

            // uz (Uzbek)
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

            // vi (Vietnamese)
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

        );

        // replace any found keys with values
        // hence using strtr, so that the corrections array is readable
        return strtr($string, $corrections);
    }
}