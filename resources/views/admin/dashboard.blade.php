@extends('adminlte::page')

@section('title', '儀表板')

@section('content_header')
    <h1>儀表板</h1>
@stop

@section('content')
    <p>歡迎來到管理後台！</p>
    <div id="calendar"></div> {{-- 這裡是未來放置行事曆的容器 --}}
@stop

@section('css')
    {{-- 如果有此頁面專屬的 CSS 可寫在此 --}}
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                }
            });
            calendar.render();
        });
    </script>
@stop