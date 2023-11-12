import { Head, router } from '@inertiajs/react';
import { useEffect } from 'react';
import Pusher from 'pusher-js';

export default function Borne({ borne, services, today}) {

    function takeTicket(service) {
        router.post(route('ticket.create', { id: service.id}));
    }

    useEffect(() => {
        Pusher.logToConsole = false;

        const pusher = new Pusher('1510a76563d856aa4e91', {
            cluster: 'mt1'
        });

        const channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            if (data.message === "next-ticket" || data.message === "new-ticket") {
                router.reload({ only: ['services'] });
            }
        });
    },[]);

    return (
        <>
            <Head title="Borne" />

            <img alt="fond" src={`storage/${borne.fond}`} className="wallpaper"/>
            <div className="container-services">
            {
               services.map((service, index) => 
                    <div key={index} className="container-services__element"
                            onClick={() => takeTicket(service)}>
                        <img alt={service.nom} src={`storage/${service.photo}`}
                                className="container-services__image"/>
                        <h2>{service.nom}</h2>
                        <p>{service.restant} Personne(s) en attente</p>
                    </div>
                 ) 
            }
                 
            </div>
        </>
    );
}
