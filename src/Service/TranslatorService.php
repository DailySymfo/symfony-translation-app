<?php
namespace App\Service;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TranslatorService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params=$params;
    }
    
    public function getTranslate($leftLang, $rightLang, $text_to_translate): string
    {
        
         $tab_lang=json_decode(file_get_contents($this->params->get('lang_code')));


         foreach($tab_lang->lang as $tb)
         {
             if($tb->name === $leftLang)
             {
                 $leftLang=$tb->code;
             }

             if($tb->name === $rightLang)
             {
                $rightLang =$tb->code;
             }
         }


         $pipeline=new GoogleTranslate();

         $translation=$pipeline->setSource($leftLang)->setTarget($rightLang)->translate($text_to_translate);

        return $translation;
    }
}