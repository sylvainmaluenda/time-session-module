import { createRoot } from 'react-dom/client';
import '../index.css';
import App from './App.tsx';

const root = document.getElementById('session-widget-root');

if (!root) {
    throw new Error('Root element not found.');
}

const props = JSON.parse(root.dataset.props!);

createRoot(root).render(<App {...props} />);
