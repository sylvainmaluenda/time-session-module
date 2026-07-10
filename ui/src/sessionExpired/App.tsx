import { ReviewForm } from './components/ReviewForm';

interface Props {
    reviewUrl: string;
}

export default function App({ reviewUrl }: Props) {
    return (
        <main className="flex min-h-screen items-center justify-center bg-slate-100 px-6">
            <section className="w-full max-w-md rounded-xl p-10">
                <div className="text-center">
                    <h1 className="text-3xl font-bold text-slate-900">Time&apos;s up!</h1>

                    <p className="mt-4 text-slate-600">Thanks for checking our demo.</p>
                </div>

                <div className="border-t border-gray-400 mt-4 pt-4">
                    <ReviewForm reviewUrl={reviewUrl} />
                </div>
            </section>
        </main>
    );
}
