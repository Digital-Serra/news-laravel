<?php namespace App\Http\Controllers\Dashboard;

use DigitalSerra\NewsLaravel\Entities\News;
use DigitalSerra\NewsLaravel\Entities\Picture;
use DigitalSerra\NewsLaravel\Entities\Tag;
use DigitalSerra\NewsLaravel\Http\Requests\NewsFormRequest;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\File;
use DateTime;


class NewsController extends Controller
{

    public function __construct()
    {
    }

    /**
     * @return $this
     */
    public function index()
    {
        $page_name = "Notícias e eventos";
        return view('dashboard.news.index')
            ->with('page_name', $page_name)
            ->with('allNews', News::with('pictures')
                ->with('tags')
                ->paginate(6));
    }

    /**
     * @return $this
     */
    public function add()
    {
        $page_name = "Nova Notícia";
        return view('dashboard.news.new')
            ->with('page_name', $page_name);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function post_add(NewsFormRequest $request)
    {
        $news = News::create($request->all());

        $tags = array_filter(array_map('trim', explode(',', $request->get('tags'))));

        foreach ($tags as $tag) {
            if (Tag::where('name', '=', $tag)->first() == null) {
                $newTag = Tag::create(['name' => $tag]);
                $news->tags()->attach($newTag);
            } else {
                $tagId = Tag::where('name', '=', $tag)->first()->id;
                $news->tags()->attach($tagId);
            }
        }


        // Save Images
        if (@array_shift($request->file('images')) != null) {
            foreach ($request->file('images') as $image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->move('img/news/' . $news->id . '/', $imageName);

                $picture = new Picture();
                $picture->path = $imagePath;
                $picture->ext = $image->getClientOriginalExtension();
                $picture->news_id = $news->id;

                $picture->save();
            }
        }

        Flash::success('Notícia cadastrada!');
        return redirect(route('news.index'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete($id)
    {
        $news = News::find($id);
        $deleteDir = File::deleteDirectory((base_path() . '/public/' . 'img/news/' . $news->id));
        $news->delete();
        Flash::success('Notícia deletada!');
        return redirect(route('news.index'));
    }

    /**
     * @param $id
     * @return $this
     */
    public function edit($id)
    {
        $news = News::find($id);
        $page_name = "Editar notícia";
        return view('dashboard.news.edit')
            ->with('page_name', $page_name)
            ->with('edit_news', $news)
            ->with('images', File::files('img/news/' . $news->id));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function post_edit(Request $request, $id)
    {
        $rules = [
            'title' => 'required|min:3',
            'body' => 'required|min:5',
            'published' => 'required'
        ];

        if ($request->file('images') != null) {
            foreach ($request->file('images') as $key => $val) {
                $rules['images.' . $key] = 'image';
            }
        }

        $attributes = [
            'title' => 'título',
            'body' => 'descrição',
            'published' => 'publicar',
            'images[]' => 'imagens',
            'images' => 'imagem'
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($attributes);

        if ($validator->fails()) {
            return redirect()
                ->route('news.edit',$id)
                ->withErrors($validator->errors())
                ->withInput();
        }

        //Verify if the news name exists on the database
        if(News::where('title','=',$request->get('title'))->first()){
            if(News::find($id)->title == $request->get('title')){
                // Title equal to actual news
            }else{
                Flash::error('Já existe outra notícia com o mesmo nome!');
                return redirect(route('news.edit',$id));
            }
        }

        // Get the actual news
        $news = News::find($id);
        $news->fill($request->all());

        // Save the tags
        $tags = array_filter(array_map('trim', explode(',', $request->get('tags'))));
        if($tags != []){
            foreach ($tags as $tag) {
                if (Tag::where('name', '=', $tag)->first() == null) {
                    $newTag = Tag::create(['name' => $tag]);
                    $tagsList[] = $newTag;
                } else {
                    $tagId = Tag::where('name', '=', $tag)->first()->id;
                    $tagsList[] = $tagId;
                }
            }

            $syncTags = $news->tags()->sync($tagsList);
        }else{
            $syncTags = $news->tags()->sync([]);
        }

        // Save Images
        if ($request->get('currentImages') != null) {
            if(array_shift($request->get('currentImages')) != null){
                foreach ($request->get('currentImages') as $image) {
                    $pictureIds[] = Picture::where('path','=',$image)->first()->id;
                }

                $deletePictures = DB::table('pictures')->where('news_id', $id)->whereIn('id',$pictureIds);

                foreach($deletePictures->get() as $ids){
                    File::delete((base_path() . '/public/' . $ids->path));
                    if (count(glob((base_path() . '/public/img/news/'.$id.'/*'))) === 0 ){
                        rmdir(base_path() . '/public/img/news/'.$id);
                    }
                }

                $deletePictures->delete();
            }
        }

        // Save new Images
        if (array_shift($request->file('images')) != null) {
            foreach ($request->file('images') as $image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->move('img/news/' . $news->id . '/', $imageName);

                $picture = new Picture();
                $picture->path = $imagePath;
                $picture->ext = $image->getClientOriginalExtension();
                $picture->news_id = $news->id;

                $picture->save();
            }
        }

        Flash::success('Notícia atualizada!');
        return redirect(route('news.index'));
    }

}