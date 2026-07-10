import { CheckCircle2 } from "lucide-react";
import { cn } from "@/lib/utils";

type Props = {
  message?: string | null;
  className?: string;
};

export function ActionSuccess({ message, className }: Props) {
  if (!message) return null;

  return (
    <div
      role="status"
      aria-live="polite"
      className={cn(
        "flex items-center gap-2 rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 mb-6 text-sm text-emerald-600",
        className,
      )}
    >
      <CheckCircle2 className="h-4 w-4 shrink-0" />
      <span>{message}</span>
    </div>
  );
}
