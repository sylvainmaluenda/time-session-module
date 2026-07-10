import { useState } from 'react';

interface Props {
    value: number;
    onChange: (rating: number) => void;
}

export default function StarRating({ value = 0, onChange }: Props) {
    const [hover, setHover] = useState(0);

    return (
        <div className="flex gap-1">
            {[1, 2, 3, 4, 5].map((star) => (
                <button
                    key={star}
                    type="button"
                    onClick={() => onChange(star)}
                    onMouseEnter={() => setHover(star)}
                    onMouseLeave={() => setHover(0)}
                    className="text-3xl transition-transform hover:scale-100"
                >
                    <span className={star <= (hover || value) ? 'text-yellow-400' : 'text-gray-300'}>★</span>
                </button>
            ))}
        </div>
    );
}
