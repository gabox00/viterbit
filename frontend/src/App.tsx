import { createBrowserRouter, Navigate, RouterProvider } from 'react-router';
import { Layout } from './components/Layout';
import { ApplicationDetailPage } from './pages/ApplicationDetailPage';
import { ApplicationsPage } from './pages/ApplicationsPage';
import { ApplyPage } from './pages/ApplyPage';
import { JobPositionsPage } from './pages/JobPositionsPage';

const router = createBrowserRouter([
  {
    element: <Layout />,
    children: [
      { path: '/', element: <JobPositionsPage /> },
      { path: '/job-positions/:jobPositionHash/apply', element: <ApplyPage /> },
      { path: '/applications', element: <ApplicationsPage /> },
      { path: '/applications/:jobApplicationHash', element: <ApplicationDetailPage /> },
      { path: '*', element: <Navigate to="/" replace /> },
    ],
  },
]);

export function App() {
  return <RouterProvider router={router} />;
}
