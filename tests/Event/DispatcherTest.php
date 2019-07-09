<?php

namespace Sebdesign\SM\Test\Event;

use Illuminate\Support\Facades\Event as EventFacade;
use Sebdesign\SM\Test\Article;
use Sebdesign\SM\Test\TestCase;
use SM\Event\SMEvents;
use Symfony\Component\EventDispatcher\Event;

class DispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function it_dispatches_an_event()
    {
        // Arrange

        if (version_compare($this->app->version(), '5.8.0')) {
            $method = 'dispatch';
        } else {
            $method = 'fire';
        }

        EventFacade::shouldReceive($method)->once()->with('foo', \Mockery::type(Event::class));

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $event = $dispatcher->dispatch('foo');

        // Assert

        $this->assertInstanceOf(Event::class, $event);
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_adds_a_listener()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addListener('foo', function () {
        });
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_adds_a_subscriber()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->addSubscriber(new Subscriber());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_removes_a_listener()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        $dispatcher->removeListener('foo', function () {
        });
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_removes_a_subscriber()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->removeSubscriber(new Subscriber());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_gets_the_listeners()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListeners();
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_gets_the_listener_priority()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->getListenerPriority('foo', function () {
        });
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_checks_if_it_has_listeners()
    {
        // Arrange

        $dispatcher = $this->app->make('sm.event.dispatcher');

        // Act

        $dispatcher->hasListeners();
    }

    /**
     * @test
     */
    public function it_fires_transition_events()
    {
        // Arrange

        $this->expectsEvents([
            SMEvents::TEST_TRANSITION,
            SMEvents::PRE_TRANSITION,
            SMEvents::POST_TRANSITION,
        ]);

        $this->app['config']->set('state-machine.graphA.class', Article::class);
        $article = new Article();

        $factory = $this->app->make('sm.factory');
        $sm = $factory->get($article, 'graphA');

        // Act

        $sm->can('create');
        $sm->apply('create');
    }
}
