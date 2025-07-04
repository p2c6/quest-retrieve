export function getNestedValue(
  obj: Record<string, any> | undefined | null,
  keyPath: string,
  fallback: any = '-'
): any {
  return keyPath.split('.').reduce((acc, key) => {
    if (acc && typeof acc === 'object' && key in acc) {
      return acc[key];
    }
    return undefined;
  }, obj) ?? fallback;
}