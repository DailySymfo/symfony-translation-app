<?php

namespace App\Controller;

use App\Service\TranslatorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
   
   private $translate;


   public function __construct(TranslatorService $translate)
   {
        $this->translate=$translate;
   }
   
   
   
   
    #[Route('/', name: 'app_home')]
    public function index(? string $text_to_translate, ? string $text_translated, ? string $source_lang, ? string $target_lang): Response
    {
        
        $tab_lang=json_decode(file_get_contents($this->getParameter('lang_code')));

        if($source_lang && $target_lang)
        {
           foreach($tab_lang->lang as $tb)
           {
              if($tb->name == $source_lang)
              {
                  $leftLang=array_unique(array_merge(array('0'=>$tb),$tab_lang->lang),SORT_REGULAR);
              }

              if($tb->name == $target_lang)
              {
                  $rightLang=array_unique(array_merge(array('0'=>$tb),$tab_lang->lang),SORT_REGULAR);
              }
           }
        }
        
        else
        {
            $leftLang=$tab_lang->lang;

            $rightLang=array_reverse($tab_lang->lang,true);
        }
       
        
        
        return $this->render('home/index.html.twig',
    
          [
            'leftLang'=>$leftLang,
            'rightLang'=>$rightLang,
            'text_to_translate'=>$text_to_translate,
            'text_translated'=>$text_translated
          ]
    
    );
    }

   
   
   
    #[Route('/translate', name: 'app_translate', methods:'POST')]
    public function translate(Request $request): Response
    {
     

         $leftLang=$request->request->get('left-lang');

         $rightLang=$request->request->get('right-lang');

         $text_to_translate=$request->request->get('textsend');



         $translation=$this->translate->getTranslate($leftLang,$rightLang,$text_to_translate);

         return $this->forward('App\Controller\HomeController::index',
         [
            'text_to_translate'=>$text_to_translate,
            'text_translated'=>$translation,
            'source_lang'=>$leftLang,
            'target_lang'=>$rightLang
         ]
         
         );


    }



    #[Route('/switch', name: 'app_switch')]
    public function switchLang(Request $request): Response
    {
         $data=json_decode($request->getContent(),true);

         $leftLang=$data['leftLang'];

         $rightLang=$data['rightLang'];

         $text_to_translate=$data['text_to_translate'];

         $translation=$this->translate->getTranslate($leftLang,$rightLang,$text_to_translate);

         $table=
         [
            'text_translated'=>$translation
         ];

         return $this->json($table);


    }

}
