@extends('layouts.app-main')
@section('title', 'Game')

@section('content')
    <section class="flex flex-col w-full">
        <article class="flex-1 w-full md:w-1/2 pb-11">
            <h1 class="text-2xl font-bold text-center">{{$player->name}}</h1>
        </article>
        <article class="flex-1 w-full md:w-1/2 pb-5">
            @include('component.choice')
        </article>
        <article class="flex-1 w-full md:w-1/2">
            @include('component.inventory')
        </article>
    </section>

@endsection
