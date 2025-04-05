@push('js')
<script>
    $(document).ready(function() {
        // Monitoraggio Pace
        if (typeof Pace !== 'undefined') {
            console.log("Pace è attivo");
            
            // Forza Pace a fermarsi dopo 5 secondi se ancora attivo
            setTimeout(function() {
                if (typeof Pace !== 'undefined' && Pace.running) {
                    console.log("Forzando Pace a fermarsi dopo timeout");
                    Pace.stop();
                }
            }, 5000);
            
            // Aggiungi listener per debug
            Pace.on('start', function() {
                console.log("Pace: iniziato caricamento", new Date().toISOString());
            });
            
            Pace.on('stop', function() {
                console.log("Pace: caricamento completato", new Date().toISOString());
            });
            
            Pace.on('restart', function() {
                console.log("Pace: riavviato", new Date().toISOString());
            });
            
            // Monitora richieste AJAX
            $(document).ajaxStart(function() {
                console.log("AJAX: iniziata richiesta", new Date().toISOString());
            });
            
            $(document).ajaxStop(function() {
                console.log("AJAX: tutte le richieste completate", new Date().toISOString());
                // Assicurati che Pace si fermi quando tutte le richieste AJAX sono complete
                if (typeof Pace !== 'undefined' && Pace.running) {
                    Pace.stop();
                }
            });
            
            // Gestisci errori AJAX
            $(document).ajaxError(function(event, jqXHR, settings, error) {
                console.error("AJAX errore:", error, "URL:", settings.url, new Date().toISOString());
                if (typeof Pace !== 'undefined' && Pace.running) {
                    Pace.stop();
                }
            });
        }

        // Notifiche per messaggi di successo
        @if(session('success'))
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Successo',
                subtitle: '{{ now()->format('H:i') }}',
                body: '{{ session('success') }}',
                icon: 'fas fa-check-circle fa-lg',
                autohide: true,
                delay: 5000,
            });
        @endif
        
        // Resto del codice di notifica immutato
        @if(session('error'))
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Errore',
                subtitle: '{{ now()->format('H:i') }}',
                body: '{{ session('error') }}',
                icon: 'fas fa-exclamation-circle fa-lg',
                autohide: true,
                delay: 7000,
            });
        @endif
        
        @if(session('warning'))
            $(document).Toasts('create', {
                class: 'bg-warning',
                title: 'Attenzione',
                subtitle: '{{ now()->format('H:i') }}',
                body: '{{ session('warning') }}',
                icon: 'fas fa-exclamation-triangle fa-lg',
                autohide: true,
                delay: 6000,
            });
        @endif
        
        @if(session('info'))
            $(document).Toasts('create', {
                class: 'bg-info',
                title: 'Informazione',
                subtitle: '{{ now()->format('H:i') }}',
                body: '{{ session('info') }}',
                icon: 'fas fa-info-circle fa-lg',
                autohide: true,
                delay: 5000,
            });
        @endif
    });
</script>
@endpush