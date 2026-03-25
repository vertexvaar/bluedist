import { execFileSync } from 'child_process';

export function haveUser(username: string, password: string, roles: string[] = ['user']): void {
    execFileSync('ddev', ['exec', './vendor/bin/bluesprints', 'app:user:create', username, password], {
        cwd: process.cwd(),
        stdio: 'pipe',
    });
    for (const role of roles) {
        execFileSync('ddev', ['exec', './vendor/bin/bluesprints', 'app:user:addrole', username, role], {
            cwd: process.cwd(),
            stdio: 'pipe',
        });
    }
}

export function cleanupVarDir(): void {
    execFileSync('ddev', ['exec', 'rm', '-rf', 'var'], {
        cwd: process.cwd(),
        stdio: 'pipe',
    });
}
