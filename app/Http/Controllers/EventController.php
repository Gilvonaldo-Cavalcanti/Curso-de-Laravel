<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Método de pesquisa da Home (verifica a pesquisa feita pelo usuário e retorna 
     * a view welcome, passando a pesquisa e os eventos pesquisados).
     */
    public function index(){
        
        // Obtendo a pesquisa feita pelo usuário 
        $search = request('search');

        // Verificando se foi feito a pesquisa
        if ($search){

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        }else {
            $events = Event::all();
        }

        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    /**
     * Método de criação de eventos
     */
    public function create() {
        // Retornando a página de criação de eventos
        return view('events.create');
    }

    /**
     * Método de adição de imagem ao evento 
     */
    public function store(Request $request){

        // Instanciando um novo evento
        $event = new Event;

        // Atribuindo as propriedade passadas por parâmentro pelo request para o novo evento
        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Atualização da Imagem 
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')). "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);
            $event->image = $imageName;
        }
        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Atibuindo o id do usuário atual ao "event_id"
        $event->user_id = $user->id;

        // Salvando o evento
        $event->save();
        
        // Redirecionando o usuário para a raíz do sistema e passando uma msg de confirmação
        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    /**
     * Método da página de um evento (exibição detalhada de um evento)
     */
    public function show($id){

        // Atribuindo um evento, buscado pelo id passado no parâmetro, a variável event
        $event = Event::findOrFail($id);

        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Boleano que indica se o usuário atual já está participando deste evento
        $hasUserJoined = false;

        // Verificação da participação do usuário atual neste evento
        if($user){
            $userEvents = $user->eventsAsParticipant->toArray();

            foreach($userEvents as $userEvent){
                if($userEvent['id'] == $id){
                    $hasUserJoined = true;
                }
            }
        }
        // Atribuindo o dono do evento a variável eventOwner (primeiro que encontrar).
        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

        // Retornando a view de exibição de evento e passando as variáveis "evento", "dono do evento" e "se o usuário está participando do evento atual".
        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]);
    }

    /**
     * Método da view dashboard (apenas usuários logados têm acesso a dashboard)
     */
    public function dashboard() {

        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Obtendo os eventos do usuário atual e atribuído a variável events
        $events = $user->events;

        // Obtendo os eventos que o usuário atual está participando e atribuindo a variável eventsAsParticipant
        $eventsAsParticipant = $user->eventsAsParticipant;

        // Retornando a view dashboard e passando para ela os dados de "eventos" e "eventos que o usuário está participando".
        return view('events.dashboard', ['events' => $events, 'eventasparticipant' => $eventsAsParticipant]);
    }

    /**
     * Método de exclusão de um evento específico.
     */
    public function destroy($id){

        // Obtendo o evento através do id passado no parâmentro e o excluindo.
        Event::findOrFail($id)->delete();

        // Redirecionando para a view Dashboard com uma mensagem de sucesso.
        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    /**
     * Método de edição de eventos.
     */
    public function edit($id){

        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Recuperando o evento com base no id passado por parâmetro.
        $event = Event::findOrFail($id);

        // Verificando se o usuário é o dono do evento
        if($user->id != $event->user->id){
            return redirect('/dashboard');
        }else {
            return view('events.edit', ['event' => $event]);
        }
    }

    /**
     * Método para atualização de imgens, na edição de eventos
     */
    public function update(Request $request){

        // Atribuindo todos os dados do request
        $data = $request->all();

        // Verificando se tem imagem no request e se a imagem é válida
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime('now')). "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);
            $data['image'] = $imageName;
        }

        // Recuperando o evento pelo id e atualizando a imagem
        Event::findOrFail($request->id)->update($data);

        // Redirecionando o usuário para a view dashboard e passando uma mensagem de sucesso.
        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    /**
     * Método de confirmação de presença em um evento
     */
    public function joinEvent($id){

        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Relação many to many
        $user->eventsAsParticipant()->attach($id);

        // Obtendo o evento por id
        $event = Event::findOrFail($id);

        // Redirecionando para a dashboard e passando uma mensagem de sucesso.
        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento ' . $event->title);
    }

    /**
     * Método para "sair" de um evento 
     */
    public function leaveEvent($id){

        // Obtendo o usuário logado no sistema
        $user = auth()->user();

        // Desfazendo a relação many to many
        $user->eventsAsParticipant()->detach($id);

        // Obtendo um evento através do id passado por parâmetro
        $event = Event::findOrFail($id);

        // Redirecionando para a dashboard com uma mensagem de sucesso.
        return redirect('/dashboard')->with('msg', 'Você saiu com sucesso do evento:  ' . $event->title);
    }
}


