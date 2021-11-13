## Introduction
This library is a tentative solution to a challenge with creating common packages within
StellarWP. Please look through the code and consider real use-cases.

### The Challenge - Service Containers
Happily, Service Containers are used in StellarWP's brands. The containers handle singletons,
dependency injection, and so forth. They're great and you should use them.

What's tricky, however, is that many libraries that we'd like to create in StellarWP will
sensibly want to use the Service Container for things like instantiation. But... how does
each library know which Service Container to use?

This may not sound challenging, but what exasperates the issue is the fact that each plugin
will be loading the packages via composer, but auto-loading means whichever plugin is loaded
first will have its package used.

Next, we can't simply set the container globally in the package (e.g. `Router::setContainer()`)
as it would be overridden by the next plugin. Somehow, the package has to have every container
registered and somehow know which to use at runtime.

### Tentative Solutions

#### 1. This Package
This package would be used by *all* StellarWP packages. Its sole purpose is to be the registrar
for the container and determine at runtime which to use.

Presently, it uses the callstack to make this determination. Here's how it would work:
1. Each plugin uses the `ContainerContract` interface on its Container
2. Each plugin registers its container and root namespace with `getPluginContainer`:
    ```php
    getPluginContainer('GiveWP', $containerInstance);
    ```
3. Next, when a package wants to use the service container, it calls `getPluginContainer`
4. The function uses the callstack and uses the namespace for each function/method to find
the first match for a registered container. It then returns the container instance.
5. The package then uses the container to do its thing.

**Trade-Offs**
- Depending on the size of the callstack this can have a bit of overhead
- All methods and functions within the plugins must have a namespace
- It's possible the callstack will yield nothing if the calls are made *purely* within a
plain PHP file. I think this is unlikely, though, as even a view would've been included via
a function or method.
- I haven't proven it yet, but it's *possible* that two StellarWP plugins could conflict.
In theory, if GiveWP passes a callback to TEC who then uses a package, but for some reason
the container should use GiveWP, then TEC would be mistakenly identified for the container.
This may be nothing in practice.

**Use instantiated class**
Another feature that could be introduced in this is the ability to interact with this package
as a proxy container. In that case, the namespace of the class being passed could be used to
infer which container to use. So if `GiveWP\Models\Donation` is passed to be instantiated, it
grabs the GiveWP namespace container. This would be helpful in avoiding the overhead.

The more I think about this idea the more I like it. This package would be a Registrar and
Container Proxy.

#### 2. Shared StellarWP Container
We could make a package that provides a common Service Container to be used in all packages and
directly in the plugins. This would quickly overcome this obstacle, but with some considerable
trade-offs:

**Trade-Offs**
- We would have to refactor existing plugin code to make up for differences in how the common
Service Container works.
- Conflicts. While all classes should be (are?) namespaced, it would become vital. Furthermore,
and more likely, would be problems with aliases. In GiveWP, for example, someone can do
`give()->paymentGateways` to grab the gateways register. This uses an alias in the container.
It's not at all unrealistic that this could lead to conflicts as multiple plugins vie for the
same alias. This could lead to StellarWP turf wars and choreographed dance fights. Who wants that?

#### 3. Something else?
I'm completely open to alternatives and wanted to start the conversation. The Service Container is
(I think appropriately) a central piece of the GiveWP architecture. To effectively share code in
StellarWP this challenge will need to be reliably handled.