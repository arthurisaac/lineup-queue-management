import { Head, router } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import Pusher from 'pusher-js';

export default function Tv({ videos, information, passages }) {

   const endpoint = "storage/";
   const videoRef = useRef();
   const timeRef = useRef();
   const currentIndex = useRef(0);
   //const [currentIndex, setCurrentIndex] = useState(0);
   const [currentVideo, setCurrentVideo] = useState("");
   const [toggleCallBox, setToggleCallBox] = useState(false);

   const currentTime = () => {
        setInterval(() => {
            try {
                timeRef.current.innerText = new Date().toLocaleTimeString("fr-FR");
            } catch(e) {
                console.log(e);
            }
                
        }, 1000)
    }

    const playNext = () => {

        // S'il y'a une vidéo
        if (videos.length === 1) {
            const node = videoRef.current;
            setCurrentVideo(endpoint + videos[0].nom)
            node.play();
        }
        
        // S'il y'a plus d'une vidéo
        if (videos.length > 1) {
            const node = videoRef.current;
            if (currentIndex.current < videos.length) {
                setCurrentVideo(endpoint + videos[currentIndex.current] .nom)
                currentIndex.current = currentIndex.current + 1;
            } else {
                currentIndex.current = 0;
                setCurrentVideo(endpoint + videos[currentIndex.current].nom)
            }
            node.play();
        }

        /* if (videos.length === 1) {
            console.log("1")
            const node = videoRef.current;
            setCurrentVideo(endpoint + videos[0].nom)
            node.play();
        } else if (currentIndex < videos.length - 1) {
            console.log("-1")
            let c_i = currentIndex + 1;
            setCurrentVideo(endpoint + videos[c_i].nom)
            setCurrentIndex(c_i)
            //currentIndex = c_i;
        } else {
            console.log("autre")
            if (videos[0]) {
                setCurrentVideo(endpoint + videos[0].nom)
                setCurrentIndex(0)
                //currentIndex = 0;
            } else {
                setCurrentVideo(endpoint + videos[0].nom)
                setCurrentIndex(0)
                //currentIndex = 0;
            }

        } */
    }

    const playAudio = (message) => {
        if (message) {
            const audio = new Audio(endpoint + "audios/ring.mp3");
            audio.play();
            setTimeout(() => {
    
                let speakData = new SpeechSynthesisUtterance();
                speakData.volume = 1; // From 0 to 1
                speakData.rate = 1; // From 0.1 to 10
                speakData.pitch = 2; // From 0 to 2
                speakData.text = message;
                speakData.lang = 'fr-FR';
                speakData.voice = window.speechSynthesis.getVoices()[0];
    
                speechSynthesis.speak(speakData);
            }, 3000);
        }
    }

    useEffect(() => {
        // Heure en cours
        currentTime()

    }, []);
    
    useEffect(() => {
         // Jouer la video
        const node = videoRef.current;

        node.onended = () => {
            playNext();
        };
        node.onerror = () => {
            playNext();
        };
        
    }, []);

    useEffect(() => {
        Pusher.logToConsole = false;

        const pusher = new Pusher('1510a76563d856aa4e91', {
            cluster: 'mt1'
        });

        const channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            if (data.message === "next-ticket") {
                router.reload({ only: ['passages'], 
                onSuccess: (page) => {
                    passages = page.props.passages;
                },
                onFinish: visit => {
                    playAudio(`Le ticket, numéro: ${passages[0]?.ticket?.prefix ?? ""}${passages[0]?.ticket?.numero ?? "--"}; est appelé au guichet numéro : ${passages[0].guichet ?? "--"}`);
                    setToggleCallBox(true);
                        setTimeout(() => {
                            setToggleCallBox(false);
                        }, 11000);
                }, });    
            } else if (data.message === "recall-ticket") {
                playAudio(`Le ticket, numéro: ${passages[0]?.ticket?.prefix ?? ""}${passages[0]?.ticket?.numero ?? "--"}; est appelé au guichet numéro : ${passages[0].guichet ?? "--"}`);
            } else if (data.message === "tv-settings") {
                router.reload();
            }
        });

    }, []);

    return (
        <>
            <Head title="TV" />

           <div className="displays-container">
                {
                    toggleCallBox ?  <div className="displays-container__calling-box">
                        <div className="displays-container__calling-box__title">{passages[0].service.nom ?? "--"}</div>
                            <div className="displays-container__calling-box__description">
                                Le numéro <span>{passages[0]?.ticket?.prefix ?? ""}{passages[0]?.ticket?.numero ?? "--"}</span> → guichet <span>{passages[0].guichet ?? "--"}</span>
                            </div>
                    </div> : <></>
                }
                <div className="displays-container__main" onClick={() => {
                    /*if (passages[0] !== undefined) {
                        playAudio(`Le ticket, numéro: ${passages[0]?.ticket?.prefix ?? ""}${passages[0]?.ticket?.numero ?? "--"}; est appelé au guichet numéro : ${passages[0].guichet ?? "--"}`);
                        setToggleCallBox(true);
                        setTimeout(() => {
                            setToggleCallBox(false);
                        }, 11000);
                    }*/
                }}>
                    <div className="displays-container__main__media">
                        <video src={currentVideo}
                               ref={videoRef}  
                               autoPlay
                               controls
                               className="displays-container__main__media__video"/>
                    </div>
                    <div className="displays-container__main__services">
                        {
                            passages.map((passage, index) => (
                                <div className="displays-container__main__services__callbox bg-orange-200 rounded" key={index}>
                                    <div className="displays-container__main__services__callbox__title text-blacl text-center">Numéro</div>
                                    <h1 className="displays-container__main__services__callbox__number text-black text-center">{passage?.ticket?.numero ?? '--'}</h1>
                                    <h2 className='text-center text-1xl font-bold'>{passage?.service?.nom}</h2>
                                    <div className="displays-container__main__services__callbox__guichet text-black text-center">
                                        Guichet <span>{passage.guichet}</span>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
                <div className="displays-container__information bg-black">
                    <div className="defileParent">
                        <span className="defile text-orange-400"
                              data-text={information.info}>
                            {information.info} --
                        </span>
                    </div>
                    <div
                        className="displays-container__information__time text-orange-400" ref={timeRef}>
                    </div>
                </div>
            </div>
        </>
    );
}
