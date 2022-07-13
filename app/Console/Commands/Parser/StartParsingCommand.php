<?php

namespace App\Console\Commands\Parser;

use App\Models\Article;
use Illuminate\Console\Command;
use \DiDom\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class StartParsingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:rbc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $original = 'rbc';
    protected $start_link = 'https://www.rbc.ru/';
    protected $resources = null;

    

    protected function superTrim($str){
        return preg_replace("/\s\s+/", ' ', trim($str));
    }

    protected function parseLinks()
    {
        $this->info("get : {$this->start_link}");
        $response = Http::get($this->start_link);
        $html = $response->body();
        $document = new Document($html);
        $items = $document->find('.js-news-feed-list > a.news-feed__item');
        $links = [];

        foreach ($items as $item) {
            $links[] = $item->attr('href');
        }

        return $links;
    }

    protected function parseLink($link)
    {
        $this->info("get : {$link}");
        $link_response = Http::get($link);
        $link_html = $link_response->body();

        $document = new Document($link_html);

        $data = [
            'title' => null,
            'content' => null,
            'image' => null,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'rating' => null,
            'original' => $this->original,
            'original_link' => $link,
        ];

        $title = $this->parseTitle($document);

        if($title == null){
            return null;
        } else{
            $data['title'] = $title;
        }

        
        $image = $this->parseImage($document);

        if($title !== null){
            $data['image'] = $image;
        }


        $content = $this->parseContent($document);

        if($content == null){
            return null;
        } else{
            $data['content'] = $content;
        }

        $rating = $this->parseRating($document);

        if($rating == null){
            return null;
        } else{
            $data['rating'] = $rating;
        }


        /**
         * Meta Не требуют отдельных методов, так как всегда селекторы одинаковые
         */
        $meta_title = $document->first('head meta[name="title"]');
        if(isset($meta_title)){
            $data['meta_title'] = $this->superTrim($meta_title->attr('content'));
        }

        $meta_description = $document->first('head meta[name="description"]');
        if(isset($meta_description)){
            $data['meta_description'] = $this->superTrim($meta_description->attr('content'));
        }

        $meta_keywords = $document->first('head meta[name="keywords"]');
        if(isset($meta_keywords)){
            $data['meta_keywords'] = $this->superTrim($meta_keywords->attr('content'));
        }

        return $data;
    }

    protected function parseTitle($document)
    {
        $title = $document->first('.article .article__header__title h1.article__header__title-in');
        if(isset($title)){
            return $this->superTrim($title->text());
        } else{
            $this->error("not found title, skip");
            return null;
        }
    }

    protected function parseImage($document)
    {
        $image = $document->first('.article .article__main-image img.g-image article__main-image__image');
        if(isset($image)){
            $data['image'] = $image->attr('src');
        }
    }
    protected function parseContent($document)
    {
        $content = $document->first('.article .article__text');
        if(isset($content)){
            $elementsToRemove = $content->findInDocument('[class], script, iframe, video, img');

            if(isset($elementsToRemove)){
                foreach ($elementsToRemove as $element) {
                    $element->remove();
                }
            }

            return $this->superTrim($content->innerHtml());
        } else{
            $this->error("not found content, skip");
            return null;
        }
    }
    protected function parseRating($document)
    {
        return random_int(1,10);
    }

    protected function saveResources()
    {
        $this->info('Start saving resources');

        Article::upsert($this->resources, ['original_link'], [
            'title',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'original_link',
            'original',
            'rating',
            'content',
            'image',
        ]);

        $this->info('Done!');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        
        $this->info("Start parsing: {$this->original}");
        
        $links = $this->parseLinks();

        $resources = [];
        
        foreach ($links as $link) {
            $data = $this->parseLink($link);
            
            if($data === null) {
                continue;
            }

            $resources[] = $data;
        }

        if($resources == null){
            $this->warn('Resources not found');
            return;
        }

        $this->resources = $resources;
        $this->saveResources();

    }
}
