import { NavLink, Outlet } from 'react-router';
import { classNames } from './ui/classNames';

const NAVIGATION = [
  { to: '/', label: 'Open positions' },
  { to: '/applications', label: 'Applications' },
];

export function Layout() {
  return (
    <div className="min-h-screen">
      <header className="border-b border-slate-200 bg-white">
        <nav className="mx-auto flex max-w-6xl items-center gap-8 px-4 py-4">
          <span className="text-lg font-bold text-indigo-600">Viterbit</span>
          <ul className="flex gap-6 text-sm font-medium">
            {NAVIGATION.map((item) => (
              <li key={item.to}>
                <NavLink
                  to={item.to}
                  end={item.to === '/'}
                  className={({ isActive }) => classNames('hover:text-indigo-600', isActive ? 'text-indigo-600' : 'text-slate-600')}
                >
                  {item.label}
                </NavLink>
              </li>
            ))}
          </ul>
        </nav>
      </header>
      <main className="mx-auto max-w-6xl px-4 py-8">
        <Outlet />
      </main>
    </div>
  );
}
