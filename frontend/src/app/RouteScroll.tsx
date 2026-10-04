import { useEffect } from 'react';
import { useLocation } from 'react-router';

export function RouteScroll() {
  const { pathname, hash } = useLocation();

  useEffect(() => {
    if (!hash) {
      window.scrollTo(0, 0);
      return;
    }

    const targetId = decodeURIComponent(hash.slice(1));

    function scrollToTarget(): boolean {
      const target = document.getElementById(targetId);

      if (!target) {
        return false;
      }

      target.scrollIntoView({
        block: 'start',
      });

      return true;
    }

    if (scrollToTarget()) {
      return;
    }

    const observer = new MutationObserver(() => {
      if (scrollToTarget()) {
        observer.disconnect();
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });

    return () => {
      observer.disconnect();
    };
  }, [pathname, hash]);

  return null;
}
