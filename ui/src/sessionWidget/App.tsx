import { useEffect, useState } from 'react';

interface Props {
    endDate: string;
}

export default function App({ endDate }: Props) {
    const endTime = new Date(endDate).getTime();

    const [expired, setExpired] = useState(endTime <= Date.now());

    const initialDistance = endTime - Date.now();
    const [timeLeft, setTimeLeft] = useState<number>(initialDistance > 0 ? initialDistance : 0);

    useEffect(() => {
        // si déjà expiré UI fallback
        if (endTime <= Date.now()) {
            setExpired(true);
            return;
        }

        const interval = setInterval(() => {
            const now = Date.now();
            const distance = endTime - now;

            if (distance <= 0) {
                clearInterval(interval);

                setExpired(true);

                if (!sessionStorage.getItem('session-expired-reloaded')) {
                    sessionStorage.setItem('session-expired-reloaded', '1');
                    location.reload();
                }

                return;
            }

            setTimeLeft(distance);
        }, 1000);

        return () => clearInterval(interval);
    }, [endTime]);

    if (expired) {
        return (
            <div className="fixed inset-x-0 bottom-0 bg-[#e74c3c] text-white text-center p-2.5 z-9999">
                ⛔ Session expired : only SuperAdmin can access this shop.
            </div>
        );
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((timeLeft / (1000 * 60)) % 60);
    const seconds = Math.floor(timeLeft / 1000) % 60;

    return (
        <div className="fixed inset-x-0 bottom-0 bg-[#e74c3c] text-white text-center p-2.5 z-9999">
            ⏳ Session : {days}d {hours}h {minutes}m {seconds}s
        </div>
    );
}
