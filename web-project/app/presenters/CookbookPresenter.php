<?php


namespace App\Presenters;

use App\Parsedown;
use Nittro\Bridges\NittroUI\Presenter;


class CookbookPresenter extends Presenter {

    /** @var Parsedown */
    private $parsedown;

    public function injectCookbook(Parsedown $parsedown) {
        $this->parsedown = $parsedown;
    }

    public function renderChapter($id) {
        $file = __DIR__ . '/../../../chapters/chapter_' . $id . '/chapter_' . $id . '.md';

        if (!file_exists($file)) {
            $this->error('File not found');
        }

        $this->template->contents = $this->parsedown->parse(file_get_contents($file));
    }

}
