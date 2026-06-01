import os
import re

root = r'c:\xampp\htdocs\AQUI_O_SE_MUEREN-main'
route_file = os.path.join(root, 'config', 'routes.php')
text = open(route_file, encoding='utf-8').read()
pattern = re.compile(r"['\"]([^'\"]+)['\"]\s*=>\s*\[([^\]]+)\]", re.MULTILINE)
entries = pattern.findall(text)
controllers = {}
for route, body in entries:
    ctrl = re.search(r"['\"]controller['\"]\s*=>\s*['\"]([^'\"]+)['\"]", body)
    action = re.search(r"['\"]action['\"]\s*=>\s*['\"]([^'\"]+)['\"]", body)
    if ctrl and action:
        controllers.setdefault(ctrl.group(1), set()).add(action.group(1))

controller_dir = os.path.join(root, 'controlador')
files = [f for f in os.listdir(controller_dir) if f.endswith('.php')]
controllers_in_fs = {}
for f in files:
    name = f[:-4]
    path = os.path.join(controller_dir, f)
    txt = open(path, encoding='utf-8').read()
    methods = set(re.findall(r'function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(', txt))
    controllers_in_fs[name] = {'methods': methods}

missing_files = []
missing_actions = []
for ctrl, actions in controllers.items():
    if ctrl not in controllers_in_fs:
        missing_files.append(ctrl)
    else:
        for act in actions:
            if act not in controllers_in_fs[ctrl]['methods']:
                missing_actions.append((ctrl, act))

print('missing controller files:', missing_files)
print('missing actions:', missing_actions)
