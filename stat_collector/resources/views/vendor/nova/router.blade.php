@extends('nova::layout')

@section('content')
    <div>
        <loading ref="loading"></loading>

        <transition name="fade" mode="out-in">
            <router-view class="nova-app-wrap" :key="$route.name + ($route.params.resourceName || '')"></router-view>
        </transition>

        <portal-target name="modals" multiple></portal-target>
    </div>
@endsection
