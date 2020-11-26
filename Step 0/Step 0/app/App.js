class App {

    static start() {
        //onpopstate
        window.onpopstate = () => {
            App.browse();
        }
        
        //correction burger
        $('.nav-link').on('click', (evt) => {
            let btn = $(evt.target).closest('.navbar').find('.navbar-toggler').not('.collapsed');
            btn ? btn.click() : null;
        })

        //chargement de la page
        $(document).ready(()=> {
            App.browse();
        });
    }

    static browse() {
        //récupérer le hash et l'afficher dans main
        console.clear();
        let hash = (window.location.hash || "#accueil").substring(1);
        $('#main').hide().html(hash).show();
    }



}