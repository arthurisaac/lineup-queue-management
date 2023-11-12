import { Head, router, useForm } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pusher from 'pusher-js';

export default function Caller({ auth, services, passage}) {

   const timeRef = useRef();

   const currentTime = () => {
        setInterval(() => {
            try {
                timeRef.current.innerText = new Date().toLocaleTimeString("fr-FR");
            } catch(e) {
                console.log(e);
            }
                
        }, 1000)
    }

    const recall = (service) => {
        router.post(route('recallTicket'));
    };

    const callNext = (service) => {
        router.post(route('nextTicket', {service}));
    };

    useEffect(() => {
        Pusher.logToConsole = false;

        const pusher = new Pusher('1510a76563d856aa4e91', {
            cluster: 'mt1'
        });

        const channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            router.reload();
        });
    },[]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            dashboard="caller"
        >
            <Head title="Caller" />

            <div className="grid grid-cols-4 grid-rows-5 gap-4 p-5">
                    <div >
                        <div className="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-100 mt-5">
                           
                        </div>
                    </div>
                <div>
                    <div className="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 w-100 mt-5">
                        <a href="#">
                            <h5 className="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white text-center">Service en cours</h5>
                        </a>
                        {
                            passage ? <>
                                <p className="mb-3 font-normal text-gray-700 dark:text-gray-400 text-center">{passage?.service?.nom}</p>
                                <h1 className='mt-2 text-center text-4xl font-bold dark:text-white'>{passage?.ticket?.numero}</h1>
                            </> : <p className="mb-3 font-normal text-gray-700 dark:text-gray-400 text-center text-warning">Aucun service</p>
                        }
                    </div>
                </div>
                <div >
                    <div className="grid grid-cols-2 grid-rows-5 gap-4 mt-5">
                        <div className="col-span-2">
                            <button type="button" className="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-6 py-3.5 text-center mr-2 mb-2 w-full" onClick={() => callNext('any')}>SUIVANT</button>
                        </div>
                        <div className="row-start-2"><button type="button" className="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-6 py-3.5 text-center mr-2 mb-2 w-full" onClick={recall}>RAPPELER</button></div>
                        <div className="row-start-2"><button type="button" className="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-6 py-3.5 text-center mr-2 mb-2 w-full">TRANSFERER</button></div>
                        <div className="col-span-2"><button type="button" className="text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 dark:focus:ring-teal-800 shadow-lg shadow-teal-500/50 dark:shadow-lg dark:shadow-teal-800/80  font-medium rounded-lg text-sm px-6 py-3.5 text-center mr-2 mb-2 w-full" onClick={() => callNext('any')}>SUIVANT ABSENT</button></div>
                    </div>
    
                </div>
                <div >
                    {
                        services.map((service, index) => <span key={index}>
                            <a onClick={() => {
                                callNext(service.id);
                            }} className="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 mt-5">
                                <h5 className="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{service.nom}</h5>
                                <p className="font-normal text-gray-700 dark:text-gray-400">{service.restant} restant</p>
                            </a>
                        </span>)
                    }
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
