interface AppProps {
    loginUrl: string;
}

export default function App({ loginUrl }: AppProps) {
    return (
        <main className="flex min-h-screen items-center justify-center bg-slate-100 px-6">
            <section className="w-full max-w-md rounded-xl p-10 text-center">
                <h1 className="text-3xl font-bold text-slate-900">Time&apos;s up!</h1>

                <p className="mt-4 text-slate-600">Thanks for checking our demo.</p>

                <a
                    href={loginUrl}
                    className="mt-8 inline-flex items-center justify-center rounded-md bg-blue-600 px-6 py-3 font-medium text-white transition-colors hover:bg-yellow-700"
                >
                    Connexion
                </a>
            </section>
        </main>
    );
}
