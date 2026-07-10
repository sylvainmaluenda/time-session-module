import { AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

type Props = {
    error?: string | null;
    className?: string;
};

export function ActionError({ error, className }: Props) {
    if (!error) return null;

    return (
        <div
            role="alert"
            aria-live="polite"
            className={cn(
                'flex items-center gap-2 rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 mb-6 text-sm text-destructive',
                className
            )}
        >
            <AlertCircle className="h-4 w-4 shrink-0" />
            <span>{error}</span>
        </div>
    );
}
