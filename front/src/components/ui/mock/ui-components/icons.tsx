import { iconNames } from '@/components/ui/atoms/icon/config';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import GlassPanel from '@/components/ui/atoms/glass-panel';

const Icons = () => {
  return (
    <>
      <Typography tag="h2" variant="heading-2" bold className="mt-8 mb-4 block">
        Icons
      </Typography>
      <div className="flex flex-wrap gap-8">
        <GlassPanel className="p-8 w-fit">
          <ul className="flex flex-wrap gap-6">
            {iconNames.map((iconName) => (
              <li key={iconName}>
                <Icon name={iconName} title={iconName} className="w-16 h-16" />
              </li>
            ))}
          </ul>
        </GlassPanel>
      </div>
    </>
  );
};

export default Icons;
