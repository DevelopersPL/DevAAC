$(function(){
    App = Ember.Application.create();

    App.Router.reopen({
        rootURL: '/',
        location: 'history'
    });

    App.ApplicationAdapter = DS.RESTAdapter.extend({
        namespace: 'duaac'
    });

    App.Player = DS.Model.extend({
        name: DS.attr(),
        sex: DS.attr(),
        vocation: DS.attr(),
        level: DS.attr(),
        town_id: DS.attr(),
        lastlogin: DS.attr(),
        lastlogout: DS.attr(),
        onlinetime: DS.attr()
    });

    // This bound to top-most application template
    App.ApplicationController = Ember.Controller.extend({
        isAuth: false,
        "footer-year": moment().format('YYYY'),

        login: function() {
            //var btn = $('#loading-login-btn');
            $('#loading-login-btn').button('loading');

            var request = $.post("/duaac/login", this.getProperties("name", "password"));
            request.then(this.loginsuccess.bind(this), this.loginfailure.bind(this));
        },

        loginsuccess: function(response) {
            alert("Success server response: " + response.status);
            $('#loading-login-btn').button('reset');
            this.set("isAuth", true);
        },

        loginfailure: function(response) {
            alert("Failure server response: " + response.status);
            $('#loading-login-btn').button('reset');
            this.set("isAuth", false);
        }
    });

    App.Router.map(function() {
        this.resource("account", function(){
            this.route("register", { path: "/register" });
        });
    });


    App.ApplicationRoute = Ember.Route.extend({
        setupController: function(controller) {
            // Set the IndexController's `title`
            controller.set('topCharacters', this.store.find('player'));
        }
    });

    App.IndexRoute = Ember.Route.extend({
        setupController: function(controller) {
            // Set the IndexController's `title`
            controller.set('title', "My App");
        }
    });

    // player view
    App.Router.map(function() {
        this.resource("players", function(){
            this.route("view", { path: "/:id" });
        });
    });
    App.PlayersViewRoute = Ember.Route.extend({
        model: function(params) {
            return this.store.find('player', params.id);
        }
    });


    // pages
    App.Router.map(function() {
        this.route("rules", { path: "/rules" });
        this.route("about", { path: "/about" });
    });

    App.AboutRoute = Ember.Route.extend({
        setupController: function(controller) {
            // Set the IndexController's `title`
            controller.set('title', "My App");
        }
    });


    // no purpose yet
    App.PlayerController = Ember.ObjectController.extend({

    });

    // catch all
    App.Router.map(function() {
        this.route('catchAll', { path: '*:' });
    });

    App.CatchAllRoute = Ember.Route.extend({
        redirect: function() {
            this.transitionTo('index');
        }
    });
});