<nav class="p-3 flex-column ">
    <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-700 to-gray-400">
        La- Subscription
    </h1>
    <hr class="my-3 border-gray-300"/>
    <router-link to="/">
        <div class="nav-item">
            Dashboard
        </div>
    </router-link>
    <router-link to="/plans">
        <div class="nav-item">Plans</div>
    </router-link>

    <router-link to="/plugins">
        <div class="nav-item">Plugins</div>
    </router-link>

    <router-link to="/features">
        <div class="nav-item">Features</div>
    </router-link>

    <hr class="my-3 border-gray-300"/>

    <router-link to="/subscriptions">
        <div class="nav-item">Subscriptions</div>
    </router-link>

    <breadcrumb-component></breadcrumb-component>
</nav>

<script>

</script>
